@extends('layouts.app_sneat_wali')

@section('content')

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <h5 class="card-header">Data Tagihan SPP</h5>
                <div class="card-body">
                    <!-- table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                                <tr>
                                    <th>NO</th>
                                    <th>Nama</th>
                                    <th>Jurusan</th>
                                    <th>Kelas</th>
                                    <th>Tanggal Tagihan</th>
                                    <th>Status Pembayaran</th>
                                    <th>Aksi</th>
                                </tr>
                            <tbody>
                                @forelse ($tagihan as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->siswa->nama }}</td>
                                    <td>{{ $item->siswa->jurusan }}</td>
                                    <td>{{ $item->siswa->kelas }}</td>
                                    <td>{{ $item->tanggal_tagihan->translatedFormat('F Y') }}</td>
                                    <td>{{ $item->getStatusTagihanWali() }}</td>
                                    <td>
                                        @if($item->status =='baru' || $item->status=='angsur')
                                          <a href="{{ route('wali.tagihan.show', $item->id) }}
                                            " class="btn btn-primary">Lakukan Pembayaran</a>
                                        @else
                                          <a href="" class="btn btn-success">Pembayaran Sudah Lunas</a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4">Data Tidak Ada</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection
