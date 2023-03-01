<?php

namespace App\Http\Controllers;

use App\Models\Tagihan as Model;
use App\Http\Requests\StoreTagihanRequest;
use App\Http\Requests\UpdateTagihanRequest;
use App\Models\Biaya;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\TagihanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TagihanController extends Controller
{

    private $viewindex = 'tagihan_index';
    private $viewcreate = 'tagihan_form';
    private $viewedit = 'tagihan_form';
    private $viewshow = 'tagihan_show';
    private $routeprefix = 'tagihan';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       if($request->filled('bulan') && $request->filled('tahun')){
        $models = Model::latest()
        ->whereMonth('tanggal_tagihan', $request->bulan)
        ->whereYear('tanggal_tagihan', $request->tahun)
        ->paginate(50);  
       }else{
            $models = Model::latest()->paginate(50);
       }

        return view('operator.' . $this->viewindex, [
            'models' => $models,
            'routeprefix' => $this ->routeprefix,
             'title' => 'Data Tagihan'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $siswa = Siswa::all();
        $data=[
            'model' => new Model(),
            'method' => 'POST',
            'route' => $this->routeprefix . '.store',
            'button' => 'SIMPAN',
            'title' => 'FORM DATA TAGIHAN',
            'angkatan' => $siswa->pluck('angkatan', 'angkatan'),
            'kelas' => $siswa->pluck('kelas', 'kelas'),
            'biaya' => Biaya::get(),
        ];
        return view('operator.' . $this->viewcreate, $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreTagihanRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTagihanRequest $request)
    {
        $requestData = $request->validated();
        $biayaIdArray = $requestData['biaya_id'];

        $siswa = Siswa::latest();
        if ($requestData['kelas'] != '') {
            $siswa->where('kelas', $requestData['kelas']);
        }
        if ($requestData['angkatan'] != '') {
            $siswa->where('angkatan', $requestData['angkatan']);
        }
        $siswa = $siswa->get();
        foreach ($siswa as $itemSiswa) {
            $biaya = Biaya::whereIn('id', $biayaIdArray)->get();
            unset($requestData['biaya_id']);
                $requestData['siswa_id'] = $itemSiswa->id;
                $requestData['status'] = 'baru';
                $tanggalTagihan = Carbon::parse($requestData['tanggal_tagihan']);
                $bulanTagihan = $tanggalTagihan->format('m');
                $tahunTagihan = $tanggalTagihan->format('Y');
                $cekTagihan = Model::where('siswa_id', $itemSiswa->id)
                    ->whereMonth('tanggal_tagihan', $bulanTagihan)
                    ->whereYear('tanggal_tagihan', $tahunTagihan)
                    ->first();
                    if($cekTagihan == null){
                        $tagihan = Model::create($requestData);
                        foreach ($biaya as $itemBiaya) {
                            $detail = TagihanDetail::create([
                                'tagihan_id' => $tagihan->id,
                                'nama_biaya' => $itemBiaya->nama,
                                'jumlah_biaya' => $itemBiaya->jumlah,
                            ]);
                        }
                    }
        }
        flash("Data Tagihan Berhasil Disimpan");
        return back();
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tagihan  $tagihan
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $tagihan = Model::with('pembayaran')->findOrFail($id);
        $data['tagihan'] = $tagihan;
        $data['siswa'] = $tagihan->siswa;
        $data['periode'] = Carbon::parse($tagihan->tanggal_tagihan)->translatedFormat('F Y');
        $data['model'] = new Pembayaran();
        return view('operator.' . $this->viewshow, $data);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tagihan  $tagihan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Model $tagihan)
    {
        //
    }
}
