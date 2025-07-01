@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mt-5 text-center fw-bold">Edit {{ $jenisSurat->nama_jenis }}</h2>
    <div class="my-5 shadow-lg border rounded-4 overflow-hidden">

        {{-- HEADER --}}
        <div style="background: linear-gradient(to left, #0d47a1, #42a5f5);" class="text-white p-3 d-flex align-items-center gap-3">
            <i class="bi bi-pencil-square fs-4"></i>
            <h5 class="mb-0 fw-semibold">{{ $jenisSurat->nama_jenis }}</h5>
        </div>

        {{-- FORM --}}
        <form id="form-surat" method="POST" enctype="multipart/form-data" action="{{ route('update.surat', $surat->id) }}" class="bg-white p-3">
            @csrf
            @method('PUT') {{-- Ini akan membuat Laravel menganggap ini PUT --}}

            @php
            function formatLabel($string) {
            return ucwords(str_replace(['_', '-'], ' ', $string));
            }

            $oldData = $isiData['data'] ?? $isiData ?? [];
            @endphp

            {{-- Nama Surat --}}
            <div class="mb-3">
                <label for="aksi" class="form-label fw-semibold small">Nama Surat <span class="text-danger">*</span></label>
                <input type="text" name="aksi" id="aksi" class="form-control" value="{{ $aksi }}" required>
            </div>

            @foreach ($templateFields as $section)
            <div class="mb-4">
                <h6 style="background: linear-gradient(to right, #003973, #42a5f5);" class="text-white d-inline-block rounded px-4 py-2 mb-3 shadow-sm">
                    {{ formatLabel($section['section_title']) }}
                </h6>

                @foreach ($section['fields'] as $field)
                @php
                $fieldName = $field['field_name'];
                $fieldLabel = formatLabel($fieldName);
                $fieldValue = $oldData[$fieldName] ?? '';
                @endphp

                {{-- TEXT --}}
                @if ($field['field_type'] === 'text')
                <div class="mb-3">
                    <label class="form-label fw-semibold small" for="{{ $fieldName }}">{{ $fieldLabel }} <span class="text-danger">*</span></label>
                    <input type="text" name="{{ $fieldName }}" id="{{ $fieldName }}" class="form-control" value="{{ $fieldValue }}" required>
                </div>

                {{-- TEXTAREA --}}
                @elseif ($field['field_type'] === 'textarea')
                <div class="mb-3">
                    <label class="form-label fw-semibold small" for="{{ $fieldName }}">{{ $fieldLabel }} <span class="text-danger">*</span></label>
                    <textarea name="{{ $fieldName }}" id="{{ $fieldName }}" rows="5" class="form-control">{{ $fieldValue }}</textarea>
                </div>

                {{-- SIGNATURE --}}
                @elseif ($field['field_type'] === 'signature')
                <div class="mb-3">
                    <label class="form-label fw-semibold small">{{ $fieldLabel }} (Tanda Tangan)</label>
                    @if(isset($fieldValue) && $fieldValue)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $fieldValue) }}" alt="Tanda tangan" style="max-height: 100px;">
                    </div>
                    @endif
                    <input type="file" name="{{ $fieldName }}" id="{{ $fieldName }}" class="form-control" accept="image/*">
                    {{-- Tambahkan input hidden untuk menyimpan path gambar lama --}}
                    <input type="hidden" name="existing_{{ $fieldName }}" value="{{ $fieldValue }}">
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
                                <th style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody data-repeater-list="{{ $fieldName }}">
                            @php
                        
                            $tableData = (!empty($fieldValue) && is_array($fieldValue)) ? $fieldValue : [[]];
                            @endphp
                            @foreach ($tableData as $row)
                            <tr data-repeater-item>
                                @foreach ($field['columns'] as $col)
                                <td>
                                    <input type="text"
                                        name="{{ $col['column_name'] }}"
                                        class="form-control form-control-sm"
                                        {{-- Gunakan null coalescing operator untuk mengambil nilai dari $row --}}
                                        {{-- Ini akan menangani baris data yang ada maupun baris template yang kosong --}}
                                        value="{{ $row[$col['column_name']] ?? '' }}"
                                        placeholder="{{ formatLabel($col['column_name']) }}">
                                </td>
                                @endforeach
                                <td>
                                    <button data-repeater-delete type="button" class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button data-repeater-create type="button" class="btn btn-outline-primary btn-sm mb-4">
                        <i class="bi bi-plus-circle"></i> Tambah Baris
                    </button>
                </div>

                {{-- GROUPED TABLE --}}
                @elseif ($field['field_type'] === 'grouped_table')
                <div class="grouped-table-container mb-4" data-field-name="{{ $fieldName }}">
                    <div class="group-wrapper">
                        @php
                        $groupedData = is_array($fieldValue) ? $fieldValue : [['group_title' => '', 'rows' => []]];
                        @endphp
                        @foreach ($groupedData as $groupIndex => $group)
                        <div class="group border rounded p-3 mb-3">
                            <div class="form-group mb-3">
                                <label>Group Title</label>
                                <input type="text" name="{{ $fieldName }}[{{ $groupIndex }}][group_title]" class="form-control" value="{{ $group['group_title'] ?? '' }}" placeholder="{{ formatLabel('group_title') }}">
                            </div>

                            <table class="table table-bordered rows-table">
                                <thead>
                                    <tr>
                                        @foreach ($field['grouped_columns'][0]['rows'] as $row)
                                        <th>{{ $row['value1'] }}</th>
                                        @endforeach
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="row-wrapper">
                                    @php
                                    $columns = $field['grouped_columns'][0]['rows'];
                                    $rows = $group;
                                    $rowCount = count($group[$columns[0]['value1']] ?? []);
                                    @endphp

                                    @for ($i = 0; $i < $rowCount; $i++)
                                        <tr class="row-item">
                                        @foreach ($columns as $col)
                                        <td>
                                            <input type="text" name="{{ $fieldName }}[{{ $groupIndex }}][{{ $col['value1'] }}][]" class="form-control"
                                                value="{{ $group[$col['value1']][$i] ?? '' }}" placeholder="{{ formatLabel($col['value1']) }}">
                                        </td>
                                        @endforeach
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm remove-row">Hapus Baris</button>
                                        </td>
                                        </tr>
                                        @endfor
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-secondary btn-sm add-row">+ Tambah Baris</button>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-primary add-group">+ Tambah Kelompok Baru</button>
                </div>
                @endif
                @endforeach
            </div>
            @endforeach

            <div class="d-flex justify-content-start gap-2 mt-4">
                <button type="submit" class="btn btn-success px-4 rounded-pill shadow-sm transition-all">
                    <i class="bi bi-send"></i> Simpan Perubahan
                </button>
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary rounded-pill">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Loading overlay (tetap sama) --}}
<div id="loading-overlay" class="position-fixed top-0 start-0 w-100 h-100 bg-white d-flex flex-column justify-content-center align-items-center"
    style="z-index: 1050; display: none !important;">
    <div class="spinner-border text-primary mb-3" role="status" style="width: 4rem; height: 4rem;">
        <span class="visually-hidden">Loading...</span>
    </div>
    <h5 class="text-muted">Sedang menyimpan perubahan...</h5>
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