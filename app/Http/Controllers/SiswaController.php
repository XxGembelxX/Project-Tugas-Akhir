<?php

namespace App\Http\Controllers;

use App\Models\Siswa as Model;
use App\Http\Requests\StoreSiswaRequest;
use App\Http\Requests\UpdateSiswaRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Mockery\Mock;
use PhpParser\Node\Stmt\Return_;
use Storage;

class SiswaController extends Controller
{
    private $viewindex = 'siswa_index';
    private $viewcreate = 'siswa_form';
    private $viewedit = 'siswa_form';
    private $viewshow = 'siswa_show';
    private $routeprefix = 'siswa';   
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->filled('q')){
            $models = Model::with('wali','user')->search($request->q)->paginate(50);
        }else{
            $models = Model::with('wali','user')->latest()->paginate(50);
        }
        return view('operator.' . $this->viewindex, [
            'models' => $models,
                    'routeprefix' => $this ->routeprefix,
                    'title' => 'Data Siswa'
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
            'title' => 'FORM DATA SISWA',
            'wali' => User::where('akses', 'wali')->pluck('name','id')
        ];
        return view('operator.' . $this->viewcreate, $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSiswaRequest $request)
    {
        $requestData = $request->validated();

        if ($request->hasFile('foto')) {
            $requestData['foto'] = $request->file('foto')->store('public');
        }

        if ($request->filled('wali_id')) {
            $requestData['wali_status'] = 'ok';
        }
        $requestData['user_id'] = auth()->user()->id;
        Model::create($requestData);
        flash('Data berhasil disimpan');
        return redirect()->route('siswa.index');
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
            'title' => 'FORM DATA SISWA',
            'wali' => User::where('akses', 'wali')->pluck('name','id'),
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
    public function update(UpdateSiswaRequest $request, $id)
    {
        $requestData = $request->validated();
        $model = Model::findOrFail($id);
        if ($request->hasFile('foto')) {
            Storage::delete($model->foto);
            $requestData['foto'] = $request->file('foto')->store('public');
        }

        if ($request->filled('wali_id')) {
            $requestData['wali_status'] = 'ok';
        }
        $requestData['user_id'] = auth()->user()->id;

        $model->fill($requestData);
        $model->save();
        flash('Data berhasil diubah');
        return redirect()->route('siswa.index');
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
