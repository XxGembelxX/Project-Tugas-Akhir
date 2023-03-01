@extends('layouts.app_sneat')

@section('content')

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <h5 class="card-header">DATA PEMBAYARAN</h5>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                             {!! Form::open(['route' => 'pembayaran.index', 'method' => 'GET']) !!}
                             <div class="row">
                                <div class="col-md-4 col-sm-12">
                                    {!! Form::selectMonth('bulan', request('bulan'), ['class'=>'form-control']) !!}
                                </div>
                                <div class="col-md-4 col-sm-12 ">
                                    {!! Form::selectRange('tahun', 2022, date('Y') + 1, request('tahun'), ['class'=>'form-control']) !!}
                                </div>
                                <div class="col">
                                  <button class="btn btn-primary" type="submit">Tampil</button>
                                </div>
                              </div>
                            {!! Form::close() !!}
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                                <tr>
                                    <th>NO</th>
                                    <th>NISN</th>
                                    <th>Nama</th>
                                    <th>Nama Wali </th>
                                    <th>Metode Pembayaran </th>
                                    <th>Status Konfirmasi</th>
                                    <th>Tanggal Konfirmasi</th>
                                </tr>
                            <tbody>
                                @forelse ($models as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->tagihan->siswa->nisn }}</td>
                                    <td>{{ $item->tagihan->siswa->nama }}</td>
                                    <td>{{ $item->wali->name }}
                                    <td>{{ $item->motode_pembayaran }}</td>
                                    <td>{{ $item->status_konfirmasi}}</td>
                                    <td>
                                        {!! Form::open([
                                            'route' => ['pembayaran.destroy', $item->id],
                                            'method' => 'DELETE',
                                            'onsubmit' => 'return confirm("Yakin ingin menghapus data ini?")'
                                        ]) !!}

                                        <a href="{{ route('pembayaran.show', $item->id) }}"
                                        class="btn btn-info btn-sm mx-3">
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
