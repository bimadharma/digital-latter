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
        <form method="POST" enctype="multipart/form-data" action="{{ route('surat.store', ['jenis' => $jenis]) }}" class="bg-white p-3" id="form-surat">
            @csrf

            @php
            function formatLabel($string) {
            return ucwords(str_replace(['_', '-'], ' ', $string));
            }
            @endphp

            {{-- Tambahkan input khusus Nama Surat --}}
            <div class="mb-3">
                <label for="nama_surat" class="form-label fw-semibold small">Nama Surat <span class="text-danger">*</span></label>
                <input type="text" name="nama_surat" id="nama_surat" class="form-control" placeholder="Masukkan nama surat" required>
            </div>


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
                    <textarea name="{{ $fieldName }}" id="{{ $fieldName }}" rows="5" class="form-control" placeholder="Tulis {{ $fieldLabel }} di sini..." required></textarea>
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
                                    <input type="text" name="{{ $fieldName }}[0][{{ $col['column_name'] }}]" class="form-control form-control-sm" placeholder="{{ formatLabel($col['column_name']) }}" required>
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


                {{-- GROUPED TABLE --}}
                @elseif ($field['field_type'] === 'grouped_table')
                <div class="grouped-table-container mb-4" data-field-name="{{ $field['field_name'] }}">
                    <div class="group-wrapper">
                        <div class="group border rounded p-3 mb-3">
                            <!-- Group Title -->
                            <div class="form-group mb-3">
                                <label>Group Title <span class="text-danger">*</span></label>
                                <input type="text" name="{{ $field['field_name'] }}[0][group_title]" class="form-control" placeholder="{{ formatLabel('group_title') }}" required>
                            </div>

                            <!-- Rows Table -->
                            <table class="table table-bordered rows-table">
                                <thead>
                                    <tr>
                                        @foreach ($field['grouped_columns'][0]['rows'] as $row)
                                        <th>{{ $row['value1'] }} <span class="text-danger">*</span></th>
                                        @endforeach
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="row-wrapper">
                                    <tr class="row-item">
                                        @foreach ($field['grouped_columns'][0]['rows'] as $row)
                                        <td>
                                            <input type="text" name="{{ $field['field_name'] }}[0][{{ $row['value1'] }}][]" class="form-control" placeholder="{{ formatLabel($row['value1']) }}" required>
                                        </td>
                                        @endforeach
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm remove-row">Hapus Baris</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-secondary btn-sm add-row">+ Tambah Baris</button>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary add-group">+ Tambah Kelompok Baru</button>
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
                {{-- TOMBOL PREVIEW BARU --}}
                <button type="button" id="preview-button" class="btn btn-info px-4 rounded-pill shadow-sm">
                    <i class="bi bi-eye"></i> Preview Surat
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

{{-- MODAL BARU UNTUK MENAMPILKAN PREVIEW PDF --}}
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">Preview Surat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe id="pdf-preview-frame" src="" style="width: 100%; height: 75vh;" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.grouped-table-container').each(function() {
            const container = $(this);
            const fieldName = container.data('field-name');
            let groupIndex = 1;

            // Tambah Baris di dalam satu grup
            container.on('click', '.add-row', function() {
                const group = $(this).closest('.group');
                const rowWrapper = group.find('.row-wrapper');
                const firstRow = rowWrapper.find('.row-item').first();
                const newRow = firstRow.clone();

                // Reset value input
                newRow.find('input').val('');
                rowWrapper.append(newRow);
            });

            // Hapus baris dari grup
            container.on('click', '.remove-row', function() {
                const group = $(this).closest('.group');
                const rowWrapper = group.find('.row-wrapper');
                if (rowWrapper.find('.row-item').length > 1) {
                    $(this).closest('.row-item').remove();
                } else {
                    alert("Minimal satu baris harus ada.");
                }
            });

            // Tambah Grup Baru (dengan group_title dan satu baris kosong)
            container.find('.add-group').on('click', function() {
                const firstGroup = container.find('.group').first();
                const newGroup = firstGroup.clone();

                // Ubah index dan reset nilai input
                newGroup.find('input').each(function() {
                    const oldName = $(this).attr('name');
                    const updatedName = oldName.replace(/\[\d+\]/, `[${groupIndex}]`);
                    $(this).attr('name', updatedName).val('');
                });

                container.find('.group-wrapper').append(newGroup);
                groupIndex++;
            });
        });
    });
</script>

{{-- SCRIPT BARU UNTULO PREVIEW AJAX (TANPA OVERLAY) --}}
<script>
$(document).ready(function() {
    $('#preview-button').on('click', function(event) {
        event.preventDefault();

        const form = $('#form-surat')[0];
        const formData = new FormData(form);
        const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
        const pdfFrame = $('#pdf-preview-frame');

        // Referensi ke tombol preview itu sendiri
        const previewButton = $(this);
        // Simpan teks asli tombol
        const originalButtonText = previewButton.html();

        $.ajax({
            url: "{{ route('surat.preview', ['jenis' => $jenis]) }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhrFields: {
                responseType: 'blob'
            },
            // SEBELUM AJAX DIKIRIM
            beforeSend: function() {
                // Nonaktifkan tombol dan tampilkan spinner
                previewButton.prop('disabled', true);
                previewButton.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...');
            },
            // JIKA SUKSES
            success: function(response) {
                const pdfBlob = new Blob([response], { type: 'application/pdf' });
                const pdfUrl = URL.createObjectURL(pdfBlob);
                pdfFrame.attr('src', pdfUrl);
                previewModal.show();
            },
            // JIKA GAGAL
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Error: ", textStatus, errorThrown);
                alert('Gagal membuat preview. Pastikan Group Title terisi.');
            },
            // SETELAH AJAX SELESAI (BAIK SUKSES MAUPUN GAGAL)
            complete: function() {
                // Kembalikan tombol ke kondisi semula
                previewButton.prop('disabled', false);
                previewButton.html(originalButtonText);
            }
        });
    });
});
</script>


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
