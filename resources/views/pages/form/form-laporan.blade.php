@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mt-5 text-center fw-bold">{{ $jenisSurat->nama_jenis }}</h2>
    <div class="my-5 shadow-lg">
        <div class="bg-primary text-white p-3 rounded-top d-flex align-items-center gap-2">
            <i class="bi bi-pencil-square"></i>
            <h6 class="mb-0">{{ $jenisSurat->nama_jenis }}</h6>
        </div>
        <form method="POST" enctype="multipart/form-data" action="{{ route('submit.laporan', ['jenis' => $jenis]) }}" class="bg-white p-4 rounded-bottom" id="form-surat">
            @csrf
            @foreach ($templateFields as $field)
            @php
            $fieldName = $field['field_name'];
            $fieldLabel = ucwords(str_replace(['_', '-'], ' ', $fieldName));
            @endphp

            {{-- TEXT --}}
            @if ($field['field_type'] === 'text')
            <div class="mb-3">
                <label for="{{ $fieldName }}">{{ $fieldLabel }}</label>
                <input type="text"
                    name="{{ $fieldName }}"
                    id="{{ $fieldName }}"
                    class="form-control"
                    placeholder="Tulis {{ $fieldLabel }}"
                    required>
            </div>

            {{-- TEXTAREA --}}
            @elseif ($field['field_type'] === 'textarea')
            <div class="mb-3">
                <label for="{{ $fieldName }}">{{ $fieldLabel }} (Textarea)</label>
                <textarea name="{{ $fieldName }}" id="{{ $fieldName }}" rows="5"
                    class="form-control"
                    placeholder="Tulis {{ $fieldLabel }} di sini..."></textarea>
            </div>

            {{-- SIGNATURE --}}
            @elseif ($field['field_type'] === 'signature')
            <div class="mb-3">
                <label for="{{ $fieldName }}">{{ $fieldLabel }} (Tanda Tangan)</label>
                <input type="file"
                    name="{{ $fieldName }}"
                    id="{{ $fieldName }}"
                    accept="image/*"
                    class="form-control"
                    required>
            </div>


            {{-- TABLE --}}
            @elseif ($field['field_type'] === 'table')
            <h5>{{ $fieldLabel }} (Tabel)</h5>
            <div class="repeater">
                <table class="table">
                    <thead>
                        <tr>
                            @foreach ($field['columns'] as $col)
                            @php
                            $columnName = $col['column_name'];
                            $columnLabel = ucwords(str_replace(['_', '-'], ' ', $columnName));
                            @endphp
                            <th>{{ $columnLabel }}</th>
                            @endforeach
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody data-repeater-list="{{ $fieldName }}">
                        <tr data-repeater-item>
                            @foreach ($field['columns'] as $col)
                            @php
                            $columnName = $col['column_name'];
                            $columnLabel = ucwords(str_replace(['_', '-'], ' ', $columnName));
                            @endphp
                            <td>
                                <input type="text"
                                    name="{{ $columnName }}"
                                    class="form-control"
                                    placeholder="{{ $columnLabel }}">
                            </td>
                            @endforeach
                            <td>
                                <button data-repeater-delete type="button" class="btn btn-danger btn-sm">Hapus</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button data-repeater-create type="button" class="btn btn-secondary mb-4">Tambah Baris</button>
            </div>
            @endif
            @endforeach

            <div class="d-flex justify-content-start gap-2 mt-4">
                <button type="submit" class="btn btn-success px-4 rounded-pill" id="submit-btn">Submit</button>
                <a href="{{ url()->previous() }}" class="btn btn-secondary rounded-pill">Kembali</a>
            </div>
        </form>
    </div>
</div>

{{-- Overlay Fullscreen --}}
<div id="loading-overlay"
    class="position-fixed top-0 start-0 w-100 h-100 bg-white d-flex flex-column justify-content-center align-items-center"
    style="z-index: 1050; display: none !important;">
    <div class="spinner-border text-primary mb-3" role="status" style="width: 4rem; height: 4rem;">
        <span class="visually-hidden">Loading...</span>
    </div>
    <h5 class="text-muted">Sedang diproses surat...</h5>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById('form-surat');
        const overlay = document.getElementById('loading-overlay');

        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                form.classList.add('was-validated');
                return false;
            }

            overlay.style.display = 'flex';
        });
    });
</script>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.repeater').repeater({
            initEmpty: false,
            show: function() {
                $(this).slideDown();
            },
            hide: function(deleteElement) {
                if (confirm('Yakin ingin menghapus baris ini?')) {
                    $(this).slideUp(deleteElement);
                }
            }
        });
    });
</script>

@endpush