<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\User as Model;

class WaliController extends Controller
{

    private $viewindex = 'wali_index';
    private $viewcreate = 'user_form';
    private $viewedit = 'user_form';
    private $viewshow = 'wali_show';
    private $routeprefix = 'wali';   
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('operator.' . $this->viewindex, [
            'models' => Model::wali()
                        ->latest()
                        ->paginate(50),
                    'routeprefix' => $this ->routeprefix,
                    'title' => 'Data Wali Murid'
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
            'title' => 'FORM DATA WALI MURID'
        ];
        return view('operator.' . $this->viewcreate, $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $requestData = $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users',
            'nohp' => 'required|unique:users',
            'password' => 'required'
        ]);
        $requestData['password'] = bcrypt($requestData['password']);
        $requestData['email_verified_at'] = now();
        $requestData['akses'] = 'wali';
        Model::create($requestData);
        flash('Data berhasil disimpan');
        return redirect()->route('wali.index');
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
            'siswa' => \App\Models\Siswa::pluck('nama','id'),
            'model' => Model::with('siswa')->wali()->where('id', $id)->firstOrFail(),
            'title' => 'DETAIL DATA WALI MURID'
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
            'title' => 'FORM DATA WALI MURID'
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
    public function update(Request $request, $id)
    {
        $requestData = $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email,' . $id,
            'nohp' => 'required|unique:users,nohp,' . $id,
            'password' => 'nullable'
        ]);
        $model = Model::findOrFail($id);
        if($requestData['password'] == ""){
            unset($requestData['password']);
        }else{
            $requestData['password'] = bcrypt($requestData['password']);
        }
        $model->fill($requestData);
        $model->save();
        flash('Data berhasil diubah');
        return redirect()->route('wali.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = Model::where('akses','wali')->firstOrFail();
        $model->delete();
        flash('Data berhasil dihapus');
        return back();
    }
}
