@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mt-5 text-center fw-bold">{{ $jenisSurat->nama_jenis }}</h2>

    <form method="POST" action="{{ route('submit.laporan', ['jenis' => $jenis]) }}" class="my-5">
        @csrf
        @foreach ($templateFields as $field)
        <div class="mb-3">
            <label class="form-label" for="{{ $field['field_name'] }}">
                {{ ucfirst($field['field_name']) }}
            </label>
            <input type="text"
                class="form-control"
                name="{{ $field['field_name'] }}"
                id="{{ $field['field_name'] }}"
                value=""
                required
                placeholder="Tulis {{ $field['field_name'] }}...">
        </div>
        @endforeach

        <div class="d-flex justify-content-start gap-2">
            <button type="submit" class="btn btn-success px-4 rounded-pill">Submit</button>
            <a href="{{ url()->previous() }}" class="btn btn-secondary rounded-pill">Kembali</a>
        </div>
    </form>
</div>
@endsection