<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\BankSekolah;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\User;
use App\Models\WaliBank;
use App\Notifications\Pembayaran as NotificationsPembayaran;
use App\Notifications\PembayaranNotification;
use Auth;
use DB;
use Illuminate\Http\Request;
use Notification;
use PhpParser\Node\Stmt\TryCatch;

class WaliMuridPembayaranController extends Controller
{
    public function index()
    {
        $pembayaran = Pembayaran ::where('wali_id', auth()->user()->id)
        ->latest()
        ->orderBy('tanggal_konfirmasi','desc')
        ->paginate(50);
    $data ['models'] = $pembayaran;
    return view('wali.pembayaran_index', $data);
    }

    public function show(Pembayaran $pembayaran)
    {
        auth()->user()->unreadNotifications->where('id', request('id'))->first()?->markAsRead();
        return view('wali.pembayaran_show', [
            'model' => $pembayaran,
        ]);
    }


    public function create(Request $request)
    {
        $data['listWaliBank'] = WaliBank::where('wali_id',Auth::user()->id)->get()->pluck('nama_bank_full', 'id');
        $data ['tagihan'] = Tagihan::waliSiswa()->findOrFail($request->tagihan_id);
        //$data ['bankSekolah'] = BankSekolah::findOrFail($request->bank_sekolah_id);
        $data ['model'] = new Pembayaran();
        $data ['method'] = 'POST';
        $data ['route'] = 'wali.pembayaran.store';
        $data ['listBankSekolah'] = BankSekolah::pluck('nama_bank' , 'id');
        $data ['listBank'] = Bank::pluck('nama_bank', 'id');
        if ($request->bank_sekolah_id != '') {
            $data['bankYangDipilih'] = BankSekolah::findOrFail($request->bank_sekolah_id);
        }
        $data['url'] = route('wali.pembayaran.create', [
            'tagihan_id' => $request->tagihan_id,
        ]);
        return view('wali.pembayaran_form',$data);
    }

    public function store(Request $request)
    {

        if($request->wali_bank_id == '' && $request->nomor_rekening == ''){
            flash('Silahkan pilih bank pengirim')->error();
            return back();
        }

        if($request->nama_rekening != '' && $request->nomor_rekening != ''){
            $bankId = $request->bank_id;
            $bank = Bank::findOrFail($bankId);
            if($request->filled('simpan_data_rekening')){
                $requestDataBank = $request->validate([
                    'nama_rekening' => 'required',
                    'nomor_rekening' => 'required',
                ]);
                $waliBank = WaliBank::firstOrCreate(
                    $requestDataBank,
                    [
                        'nomor_rekening' => $requestDataBank['nomor_rekening'],
                        'nama_rekening' => $requestDataBank['nama_rekening'],
                    ],
                    [
                        'nama_rekening' => $requestDataBank['nama_rekening'],
                        'wali_id' => Auth::user()->id,
                        'kode' => $bank->sandi_bank,
                        'nama_bank' => $bank->nama_bank,
                    ]
                );
            }

        }else{
            $waliBankId = $request->wali_bank_id;
            $waliBank = WaliBank::findOrFail($waliBankId);
        }
        $jumlahDibayar = str_replace('.', '', $request->jumlah_dibayar);
        $validasiPembayaran = Pembayaran::where('jumlah_dibayar', $jumlahDibayar)
            ->where('tagihan_id', $request->tagihan_id)
            ->first();

        if($validasiPembayaran != null){
            flash('Data pembayaran ini sudah ada, dan akan segera di konfirmasi oleh operator');
            return back();
        }

        $request->validate([
            'tanggal_bayar' => 'required',
            'jumlah_dibayar' => 'required',
            'bukti_bayar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5048'
        ]);
        $buktiBayar = $request->file('bukti_bayar')->store('public');
        $dataPembayaran = [
            'bank_sekolah_id' => $request->bank_sekolah_id,
            'wali_bank_id' => $waliBank->id,
            'tagihan_id' => $request->tagihan_id,
            'wali_id' => auth()->user()->id,
            'tanggal_bayar' => $request->tanggal_bayar,
            'jumlah_dibayar' => $jumlahDibayar,
            'bukti_bayar' => $buktiBayar,
            'motode_pembayaran' => 'trasfer',
            'user_id' => 0,
        ];
        DB::beginTransaction();
        try {
            $pembayaran = Pembayaran::create($dataPembayaran);
            $userOperator = User::where('akses', 'operator')->get();
            Notification::send($userOperator, new PembayaranNotification($pembayaran));
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            flash('Gagal Menyimpan Data Pembayaran, ' + $th->getMessage())->error();
            return back();
        }
        flash('Pembayaran Telah Berhasil dan Akan Segera Dikonfirmasi oleh Operator')->success();
        return back();
    }
}
