@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Detail Siswa</h5>
                        <div>
                            <a href="{{ route('siswa.edit', $siswa->id_siswa) }}" class="btn btn-primary">Edit</a>
                            <a href="{{ route('siswa.index') }}" class="btn btn-secondary">Kembali</a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="200px">ID Siswa</th>
                            <td>{{ $siswa->id_siswa }}</td>
                        </tr>
                        <tr>
                            <th>Nama Siswa</th>
                            <td>{{ $siswa->Nama_Siswa }}</td>
                        </tr>
                        <tr>
                            <th>Tempat Lahir</th>
                            <td>{{ $siswa->Tempat_Lahir ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Lahir</th>
                            <td>{{ $siswa->Tanggal_Lahir ? date('d-m-Y', strtotime($siswa->Tanggal_Lahir)) : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Jenis Kelamin</th>
                            <td>{{ $siswa->Jenis_Kelamin == 'L' ? 'Laki-laki' : ($siswa->Jenis_Kelamin == 'P' ? 'Perempuan' : '-') }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Masuk</th>
                            <td>{{ $siswa->Tanggal_Masuk ? date('d-m-Y', strtotime($siswa->Tanggal_Masuk)) : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($siswa->Status_Aktif == 1)
                                    <span class="badge bg-success">Aktif</span>
                                @elseif($siswa->Status_Aktif == 0)
                                    <span class="badge bg-danger">Tidak Aktif</span>
                                @else
                                    <span class="badge bg-secondary">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Tanggal Dibuat</th>
                            <td>{{ date('d-m-Y H:i:s', strtotime($siswa->dibuat_pada)) }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Diperbarui</th>
                            <td>{{ date('d-m-Y H:i:s', strtotime($siswa->diperbarui_pada)) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection