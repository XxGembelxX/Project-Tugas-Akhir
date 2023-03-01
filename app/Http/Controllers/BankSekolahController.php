<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBankSekolahRequest;
use App\Http\Requests\UpdateBankSekolahRequest;
use App\Models\BankSekolah as Model;
use App\Models\User;
use Illuminate\Http\Request;
use Mockery\Mock;
use PhpParser\Node\Stmt\Return_;
use Storage;

class BankSekolahController extends Controller
{
    private $viewindex = 'banksekolah_index';
    private $viewcreate = 'banksekolah_form';
    private $viewedit = 'banksekolah_form';
    private $viewshow = 'banksekolah_show';
    private $routeprefix = 'banksekolah';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
            $models = Model::paginate(50);

        return view('operator.' . $this->viewindex, [
            'models' => $models,
                    'routeprefix' => $this ->routeprefix,
                    'title' => 'Data Rekening Sekolah'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data=[
            'model' => new Model(),
            'method' => 'POST',
            'route' => $this->routeprefix . '.store',
            'button' => 'SIMPAN',
            'title' => 'FORM DATA BIAYA',
            'listBank' => \App\Models\Bank::pluck('nama_bank', 'id'),
        ];
        return view('operator.' . $this->viewcreate, $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBankSekolahRequest $request)
    {
       $requestData = $request->validated();
        $bank =\App\Models\Bank::find($request->bank_id);
        unset($requestData['bank_id']);
        $requestData['kode'] = $bank->sandi_bank;
        $requestData['nama_bank'] = $bank->nama_bank;


        Model::create($requestData);
        flash('Data berhasil disimpan');
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
       return view('operator.' . $this->viewshow,[
        'model' => Model::findOrFail($id),
        'title' => 'Detail Siswa'
       ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data=[
            'model' => Model::findOrFail($id),
            'method' => 'PUT',
            'route' => [$this->routeprefix .'.update', $id],
            'button' => 'UPDATE',
            'title' => 'FORM DATA BIAYA',

        ];
        return view('operator.' . $this->viewedit, $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBankSekolahRequest $request, $id)
    {
        $model = Model::findOrFail($id);
        $model->fill($request->validated());
        $model->save();
        flash('Data berhasil diubah');
        return redirect()->route('biaya.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = Model::firstOrFail();
        $model->delete();
        flash('Data berhasil dihapus');
        return back();
    }
}
