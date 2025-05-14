@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="my-4">Riwayat Surat</h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Jenis Surat</th>
                <th>Nomor Surat</th>
                <th>Dibuat Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($suratList as $surat)
                <tr>
                    <td>{{ $surat->jenisSurat->nama_jenis }}</td>
                    <td>{{ $surat->nomor_surat ?? '-' }}</td>
                    <td>{{ $surat->created_at->format('d-m-Y H:i') }}</td>
                    <td>
                        <!-- Opsi Cetak PDF dan Word -->
                        <a href="{{ route('cetak.surat', ['id' => $surat->id, 'format' => 'pdf']) }}" class="btn btn-danger btn-sm">Cetak PDF</a>
                        <a href="{{ route('cetak.surat', ['id' => $surat->id, 'format' => 'docx']) }}" class="btn btn-primary btn-sm">Cetak Word</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4">Belum ada surat yang dibuat.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
