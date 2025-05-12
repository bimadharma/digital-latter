@extends('layouts.app') {{-- Ganti sesuai layout kamu --}}

@section('content')
<div class="container my-5">
    <h3 class="mb-4 text-center text-primary fw-bold">Form Surat Keterangan</h3>
    
    <form action="#" method="POST">
        @csrf
        <div class="mb-3">
            <label for="nama" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" id="nama" name="nama" required>
        </div>

        <div class="mb-3">
            <label for="nik" class="form-label">NIK</label>
            <input type="text" class="form-control" id="nik" name="nik" required>
        </div>

        <div class="mb-3">
            <label for="keperluan" class="form-label">Keperluan</label>
            <textarea class="form-control" id="keperluan" name="keperluan" rows="3" required></textarea>
        </div>

        <button type="submit" class="btn btn-success px-4 rounded-pill">Submit</button>
    </form>
</div>
@endsection
