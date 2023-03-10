@extends('layouts.app_sneat')

@section('content')

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <h5 class="card-header">{{ $title }}</h5>
                <div class="card-body">
                    <a href="{{ route($routeprefix . '.create') }}" class="btn btn-primary btn-sm mb-2">Tambah Data +</a>
                    <!-- table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                                <tr>
                                    <th>NO</th>
                                    <th>Nama</th>
                                    <th>No.Hp</th>
                                    <th>Email</th>
                                    <th>Akses</th>
                                    <th>Aksi</th>
                                </tr>
                            <tbody>
                                @forelse ($models as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->nohp }}</td>
                                    <td>{{ $item->email }}</td>
                                    <td>{{ $item->akses }}</td>
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
