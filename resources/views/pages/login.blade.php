@extends('layouts.app')

@section('title', 'Home - D-SURATX')

@section('content')
<div class="bg-overlay mt-4">
    <div class="d-flex justify-content-center align-items-center">
        <div class="card shadow p-5" style="width: 100%; max-width: 600px;">
            <h3 class="text-center mb-4">Selamat Datang!</h3>

            @if (session('error'))
            <p class="text-danger text-center">{{ session('error') }}</p>
            @endif

            <form method="POST" action="{{ route('login.action') }}">
                @csrf
                <div class="mb-3">
                    <input type="text" name="username" placeholder="Username" required class="form-control rounded-pill" style="font-family: 'FontAwesome', 'Arial';">
                </div>
                <div class="mb-3">
                    <input type="password" name="password" placeholder="Password" required class="form-control rounded-pill" style="font-family: 'FontAwesome', 'Arial';">
                </div>
                <button type="submit" class="btn btn-primary rounded-pill w-100">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                </button>
            </form>

            <!-- Garis horizontal dengan tulisan dan tombol kembali -->
            <div class="d-flex align-items-center my-3">
                <hr class="flex-grow-1">
                <span class="px-2 text-muted">atau</span>
                <hr class="flex-grow-1">
            </div>

            <a href="/" class="btn btn-outline-secondary rounded-pill w-100">
                <i class="bi bi-arrow-left-circle me-1"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>
@endsection