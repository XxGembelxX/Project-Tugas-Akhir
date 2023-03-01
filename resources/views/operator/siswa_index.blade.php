@extends('layouts.app_sneat')

@section('content')

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <h5 class="card-header">{{ $title }}</h5>
                <div class="card-body">
                    <a href="{{ route($routeprefix . '.create') }}" class="btn btn-primary btn-sm mb-2">Tambah Data +</a>
                    {!! Form::open(['route' => $routeprefix . '.index', 'method' => 'GET']) !!}
                        <div class="input-group mb-2">
                            <input name="q" type="text" class="form-control" placeholder="Cari Nama Siswa" 
                            aria-label="Cari Nama Siswa" aria-describedby="button-addon2" value="{{ request('q') }}">
                            <button class="btn btn-outline-primary" type="button" id="button-addon2"><i class="bx bx-search"></i> </button>
                        </div>
                    {!! Form::close() !!}
                    <!-- table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                                <tr>
                                    <th>NO</th>
                                    <th>Nama Wali Murid</th>
                                    <th>Nama</th>
                                    <th>NISN</th>
                                    <th>Jurusan</th>
                                    <th>Kelas</th>
                                    <th>Angkatan</th>
                                    <th>Dibuat oleh</th>
                                    <th>Aksi</th>
                                </tr>
                            <tbody>
                                @forelse ($models as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->wali->name }}</td>
                                    <td>{{ $item->nama }}</td>
                                    <td>{{ $item->nisn }}</td>
                                    <td>{{ $item->jurusan }}</td>
                                    <td>{{ $item->kelas }}</td>
                                    <td>{{ $item->angkatan }}</td>
                                    <td>{{ $item->user->name }}</td>
                                    <td>
                                        {!! Form::open([
                                            'route' => [$routeprefix . '.destroy', $item->id],
                                            'method' => 'DELETE',
                                            'onsubmit' => 'return confirm("Yakin ingin menghapus data ini?")'
                                        ]) !!}
                                        <a href="{{ route($routeprefix . '.edit', $item->id) }}" class="btn btn-warning btn-sm">
                                           <i class="fa fa-edit"> </i> Edit
                                        </a>

                                        <a href="{{ route($routeprefix . '.show', $item->id) }}" class="btn btn-info btn-sm">
                                            <i class="fa fa-info"> </i> Detail
                                         </a>

                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"> </i> Hapus 
                                        </button>
                                        {!! Form::close() !!}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4">Data Tidak Ada</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        {!! $models->links() !!}
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection
