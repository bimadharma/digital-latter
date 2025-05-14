@extends('layouts.app')

@section('title', 'Home - D-SURATX')

@section('content')
<div class="bg-overlay">
    <div class="container my-5">
        <div class="row align-items-center">
            {{-- Kolom Gambar --}}
            <div class="col-md-6 order-1 order-md-2 text-center mb-4 mb-md-0">
                <img src="{{ asset('images/wallpaper-home1.png') }}" alt="Ilustrasi D-SURATX" class="img-fluid" style="max-height: 350px;">
            </div>

            {{-- Kolom Konten --}}
            <div class="col-md-6 order-2 order-md-1">
                <h1 class="fw-bold" style="color: #507dae;">D-SURATX – Digitalisasi Form Surat Eximbank</h1>
                <p class="lead">Kelola, isi, dan unduh surat resmi dengan cepat dan praktis dalam satu aplikasi terintegrasi</p>
                <hr>
                <p class="mt-3 small">
                    <strong>D-SURATX</strong> adalah aplikasi web untuk mempermudah administrasi surat di instansi, sekolah, atau organisasi. Pengguna bisa mengisi formulir, menyimpan riwayat, dan mengunduh surat PDF tanpa Microsoft Word.
                </p>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-body text-center">
                                <h5 class="card-title">Jumlah Surat Dibuat</h5>
                                <p class="display-6 fw-bold text-primary">{{ $jumlahSurat }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mt-3 mt-md-0">
                        <div class="card shadow-sm">
                            <div class="card-body text-center">
                                <h5 class="card-title">Jumlah Jenis Surat</h5>
                                <p class="display-6 fw-bold text-success">{{ $jenisSurat }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tambahan di bawah bagian "Jumlah Jenis Surat" --}}
<div class="row mt-5">
    <div class="col-12">
        <h3 class="fw-bold mb-4 text-center" style="color: #507dae;">Jenis-Jenis Surat</h3>
    </div>
    <hr>
    <div class="row justify-content-center mb-4">
        <div class="col-md-6">
            <form method="GET" action="{{ route('pages.index') }}">
                <div class="input-group">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control rounded-start-pill" placeholder="Cari jenis surat...">
                    <button class="btn btn-primary rounded-end-pill" type="submit">
                        <i class="bi bi-search"></i> Cari
                    </button>
                </div>
            </form>
        </div>
    </div>
    @if($cards->isEmpty())
    <div class="text-center my-4">
        <p class="text-muted">Tidak ada jenis surat yang cocok dengan pencarianmu.</p>
    </div>
    @endif

    @foreach($cards as $card)
    <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-sm border-0 rounded-4" style="background-color: #507dae;">
            <div class="card-body d-flex flex-column p-4 text-white">
                <h5 class="card-title text-center fw-semibold">{{ $card['title'] }}</h5>
                <p class="card-text text-center flex-grow-1">{{ $card['desc'] }}</p>
                <div class="d-flex justify-content-center gap-2 mt-auto">
                    <a href="{{ $card['href'] }}" class="btn btn-success btn-sm rounded-pill px-3 text-white">
                        Buat Surat
                    </a>
                    <a href="{{ $card['template_url'] }}" class="btn btn-outline-light btn-sm rounded-pill px-3 fw-semibold">
                        Unduh Template
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection