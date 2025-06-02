@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mt-5 text-center fw-bold">{{ $jenisSurat->nama_jenis }}</h2>
    <div class="my-5 shadow-lg border rounded-4 overflow-hidden">
        {{-- HEADER --}}
        <div style="background: linear-gradient(to left, #0d47a1, #42a5f5);"
            class="text-white p-3 d-flex align-items-center gap-3">
            <i class="bi bi-pencil-square fs-4"></i>
            <h5 class="mb-0 fw-semibold">{{ $jenisSurat->nama_jenis }}</h5>
        </div>

        {{-- FORM --}}
        <form method="POST" enctype="multipart/form-data" action="{{ route('submit.laporan', ['jenis' => $jenis]) }}" class="bg-white p-3" id="form-surat">
            @csrf

            @php
            function formatLabel($string) {
            return ucwords(str_replace(['_', '-'], ' ', $string));
            }
            @endphp

            @foreach ($templateFields as $section)
            <div class="mb-4">
                <h6 style="background: linear-gradient(to right, #003973, #42a5f5);"
                    class="text-white d-inline-block rounded px-4 py-2 mb-3 shadow-sm">
                    {{ formatLabel($section['section_title']) }}
                </h6>

                @foreach ($section['fields'] as $field)
                @php
                $fieldName = $field['field_name'];
                $fieldLabel = formatLabel($fieldName);
                @endphp

                {{-- TEXT --}}
                @if ($field['field_type'] === 'text')
                <div class="mb-3">
                    <label class="form-label fw-semibold small" for="{{ $fieldName }}">{{ $fieldLabel }} <span class="text-danger">*</span></label>
                    <input type="text" name="{{ $fieldName }}" id="{{ $fieldName }}" class="form-control" placeholder="Tulis {{ $fieldLabel }}" required>
                </div>

                {{-- TEXTAREA --}}
                @elseif ($field['field_type'] === 'textarea')
                <div class="mb-3">
                    <label class="form-label fw-semibold small" for="{{ $fieldName }}">{{ $fieldLabel }} <span class="text-danger">*</span></label>
                    <textarea name="{{ $fieldName }}" id="{{ $fieldName }}" rows="5" class="form-control" placeholder="Tulis {{ $fieldLabel }} di sini..."></textarea>
                </div>

                {{-- SIGNATURE --}}
                @elseif ($field['field_type'] === 'signature')
                <div class="mb-3">
                    <label class="form-label fw-semibold small" for="{{ $fieldName }}">{{ $fieldLabel }} (Tanda Tangan)</label>
                    <input type="file" name="{{ $fieldName }}" id="{{ $fieldName }}" accept="image/*" class="form-control">
                </div>

                {{-- TABLE --}}
                @elseif ($field['field_type'] === 'table')
                <h6 class="fw-bold mt-3">{{ $fieldLabel }} (Tabel)</h6>
                <div class="repeater">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                @foreach ($field['columns'] as $col)
                                <th>{{ formatLabel($col['column_name']) }} <span class="text-danger">*</span></th>
                                @endforeach
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody data-repeater-list="{{ $fieldName }}">
                            <tr data-repeater-item>
                                @foreach ($field['columns'] as $col)
                                <td>
                                    <input type="text" name="{{ $col['column_name'] }}" class="form-control form-control-sm" placeholder="{{ formatLabel($col['column_name']) }}">
                                </td>
                                @endforeach
                                <td>
                                    <button data-repeater-delete type="button" class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <button data-repeater-create type="button" class="btn btn-outline-primary btn-sm mb-4">
                        <i class="bi bi-plus-circle"></i> Tambah Baris
                    </button>
                </div>
                @endif
                @endforeach
            </div>
            @endforeach

            {{-- BUTTONS --}}
            <div class="d-flex justify-content-start gap-2 mt-4">
                <button type="submit" class="btn btn-success px-4 rounded-pill shadow-sm transition-all">
                    <i class="bi bi-send"></i> Submit
                </button>
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary rounded-pill">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
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