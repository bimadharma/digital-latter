@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="my-4">Riwayat Surat</h2>

    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive my-5">
        <table id="riwayatTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nama Surat</th>
                    <th>Jenis Surat</th>
                    <th>Nomor Surat</th>
                    <th>Tanggal dibuat </th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($historyList as $history)
                <tr>
                    <td>{{ $history->aksi }}</td>
                    <td>{{ $history->surat->jenisSurat->nama_jenis ?? '-' }}</td>
                    <td>{{ $history->surat->nomor_surat ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($history->waktu_aksi)->format('d-m-Y H:i') }}</td>
                    <td>
                        <a href="{{ route('edit.surat', $history->surat->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <a href="{{ route('cetak.surat', ['id' => $history->surat->id, 'format' => 'pdf']) }}" class="btn btn-danger btn-sm">PDF</a>
                        <a href="{{ route('cetak.surat', ['id' => $history->surat->id, 'format' => 'docx']) }}" class="btn btn-primary btn-sm">Word</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4">Belum ada surat yang dibuat.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#riwayatTable').DataTable({
            pageLength: 10,
            lengthMenu: [10, 50, 100],
            order: [
                [2, 'desc']
            ],
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ entri",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Berikutnya",
                    previous: "Sebelumnya"
                },
                emptyTable: "Tidak ada data tersedia",
                zeroRecords: "Tidak ditemukan data yang sesuai"
            }
        });
    });
</script>
@endsection