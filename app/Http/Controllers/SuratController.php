<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use App\Models\JenisSurat;
use Illuminate\Support\Str;
use App\Models\HistorySurat;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\TemplateProcessor;


class SuratController extends Controller
{
    /**
     * Show the form for creating a new letter based on its type.
     */
    public function create($jenis)
    {
        $jenisSurat = JenisSurat::where('kode_jenis', $jenis)->firstOrFail();
        $templateFields = $jenisSurat->template_fields;
        return view("pages.form.form-laporan", compact('templateFields', 'jenisSurat', 'jenis'));
    }

    /**
     * Generate a preview of the letter in PDF format.
     */
    public function preview(Request $request, $jenis)
    {
        $jenisSurat = JenisSurat::where('kode_jenis', $jenis)->firstOrFail();
        $isiData = $request->except('_token');
        $templatePath = storage_path('app/public/' . $jenisSurat->template_file);

        if (!file_exists($templatePath)) {
            return response()->json(['error' => 'Template surat tidak ditemukan.'], 404);
        }

        $templateProcessor = $this->populateTemplate(new TemplateProcessor($templatePath), $request, $isiData);

        // Save to a temporary location
        $timestamp = time();
        $tempDocxName = "preview_{$jenis}_{$timestamp}.docx";
        $tempPdfName = "preview_{$jenis}_{$timestamp}.pdf";
        $tempDir = storage_path('app/public/temp');

        if (!File::isDirectory($tempDir)) {
            File::makeDirectory($tempDir, 0755, true, true);
        }

        $tempDocxPath = "{$tempDir}/{$tempDocxName}";
        $tempPdfPath = "{$tempDir}/{$tempPdfName}";

        $templateProcessor->saveAs($tempDocxPath);

        // Convert to PDF
        $command = '"C:\\Program Files\\LibreOffice\\program\\soffice.exe" --headless --convert-to pdf --outdir ' . escapeshellarg($tempDir) . ' ' . escapeshellarg($tempDocxPath);
        exec($command, $output, $resultCode);

        if ($resultCode !== 0 || !file_exists($tempPdfPath)) {
            if (file_exists($tempDocxPath)) unlink($tempDocxPath);
            return response()->json(['error' => 'Gagal mengonversi file untuk preview.'], 500);
        }

        // Send file and delete it after sending
        return response()->file($tempPdfPath)->deleteFileAfterSend(true);
    }

    /**
     * Store a newly created letter in storage.
     */
    public function store(Request $request, $jenis)
    {
        $jenisSurat = JenisSurat::where('kode_jenis', $jenis)->firstOrFail();
        $isiData = $request->except('_token');
        $templatePath = storage_path('app/public/' . $jenisSurat->template_file);

        if (!file_exists($templatePath)) {
            abort(404, 'Template surat tidak ditemukan.');
        }

        $templateProcessor = new TemplateProcessor($templatePath);

        // Populate template and update isiData with file paths
        $templateProcessor = $this->populateTemplate($templateProcessor, $request, $isiData, true);

        // Generate file names
        $slugNama = Str::slug($jenisSurat->nama_jenis);
        $timestamp = time();
        $docxName = "{$slugNama}-{$timestamp}.docx";
        $pdfName = "{$slugNama}-{$timestamp}.pdf";
        $docxPath = storage_path("app/public/generated/{$docxName}");
        $pdfPath = storage_path("app/public/generated/{$pdfName}");

        $templateProcessor->saveAs($docxPath);

        // Convert to PDF
        $command = '"C:\\Program Files\\LibreOffice\\program\\soffice.exe" --headless --convert-to pdf --outdir ' . escapeshellarg(dirname($pdfPath)) . ' ' . escapeshellarg($docxPath);
        exec($command, $output, $resultCode);

        if ($resultCode !== 0 || !file_exists($pdfPath)) {
            return back()->with('error', 'Gagal mengonversi DOCX ke PDF.');
        }

        // Create letter number
        $newNumber = str_pad(Surat::count() + 1, 2, '0', STR_PAD_LEFT);
        $nomorSurat = "SURAT-EXIM-{$newNumber}";

        // Save to database
        $surat = Surat::create([
            'user_id' => Auth::id(),
            'email' => Auth::user()->email,
            'jenis_surat_id' => $jenisSurat->id,
            'nomor_surat' => $nomorSurat,
            'isi_data' => json_encode($isiData),
            'file_docx' => "generated/{$docxName}",
            'file_pdf' => "generated/{$pdfName}",
        ]);

        HistorySurat::create([
            'surat_id' => $surat->id,
            'user_id' => Auth::id(),
            'aksi' => $request->input('nama_surat', $jenisSurat->nama_jenis),
            'waktu_aksi' => $surat->created_at,
        ]);

        return redirect()->route('history.index')->with('success', 'Surat berhasil disimpan dan file PDF telah dibuat.');
    }

    /**
     * Show the form for editing the specified letter.
     */
    public function edit($id)
    {
        $surat = Surat::with('jenisSurat')->findOrFail($id);
        $history = HistorySurat::where('surat_id', $id)->first();

        return view('pages.form.form-edit', [
            'surat' => $surat,
            'jenis' => $surat->jenisSurat->kode_jenis,
            'templateFields' => $surat->jenisSurat->template_fields,
            'isiData' => json_decode($surat->isi_data, true),
            'jenisSurat' => $surat->jenisSurat,
            'aksi' => $history ? $history->aksi : null,
        ]);
    }

    /**
     * Update the specified letter in storage.
     */
    public function update(Request $request, $id)
    {
        $surat = Surat::with('jenisSurat')->findOrFail($id);
        $jenisSurat = $surat->jenisSurat;
        $oldIsiData = json_decode($surat->isi_data, true);
        $isiData = $request->except(['_token', '_method', 'aksi']);

        // Preserve old signature files if new ones are not uploaded
        $signatureFields = collect($jenisSurat->template_fields)->flatMap(fn($section) => $section['fields'])->where('field_type', 'signature')->pluck('field_name');
        foreach ($signatureFields as $field) {
            if (!$request->hasFile($field) && isset($oldIsiData[$field])) {
                $isiData[$field] = $oldIsiData[$field];
            }
        }

        $templatePath = storage_path('app/public/' . $jenisSurat->template_file);
        if (!file_exists($templatePath)) {
            return back()->with('error', 'Template tidak ditemukan.');
        }

        // Repopulate template with updated data
        $templateProcessor = $this->populateTemplate(new TemplateProcessor($templatePath), $request, $isiData, true);

        // Overwrite existing files
        $docxPath = storage_path('app/public/' . $surat->file_docx);
        $pdfPath = storage_path('app/public/' . $surat->file_pdf);

        $templateProcessor->saveAs($docxPath);

        // Re-convert to PDF
        $command = '"C:\\Program Files\\LibreOffice\\program\\soffice.exe" --headless --convert-to pdf --outdir ' . escapeshellarg(dirname($pdfPath)) . ' ' . escapeshellarg($docxPath);
        exec($command, $output, $resultCode);

        // Update database
        $surat->update(['isi_data' => json_encode($isiData)]);

        HistorySurat::updateOrCreate(
            ['surat_id' => $surat->id],
            ['aksi' => $request->input('aksi'), 'waktu_aksi' => $surat->updated_at, 'user_id' => Auth::id()]
        );

        return redirect()->route('history.index')->with('success', 'Surat dan file berhasil diperbarui.');
    }

    /**
     * Provide the specified letter for download.
     */
    public function print(Request $request, $id)
    {
        $surat = Surat::findOrFail($id);
        $format = $request->query('format', 'pdf');
        $filePath = $format === 'docx' ? $surat->file_docx : $surat->file_pdf;

        if ($filePath) {
            $path = storage_path("app/public/{$filePath}");
            if (file_exists($path)) {
                return response()->download($path);
            }
        }
        abort(404, 'File tidak ditemukan.');
    }

    /**
     * Helper function to populate a PHPWord template.
     */
    private function populateTemplate(TemplateProcessor $templateProcessor, Request $request, array &$isiData, bool $saveFiles = false): TemplateProcessor
    {
        foreach ($isiData as $key => $value) {
            if ($request->hasFile($key)) {
                $storagePath = $saveFiles ? 'signatures' : 'temp_signatures';
                $path = $request->file($key)->store($storagePath, 'public');
                $fullPath = storage_path('app/public/' . $path);
                $templateProcessor->setImageValue($key, ['path' => $fullPath, 'width' => 170, 'height' => 113, 'ratio' => true]);
                if ($saveFiles) $isiData[$key] = $path; // Store path in data array only when saving
            } elseif (is_array($value)) {
                $this->processArrayValue($templateProcessor, $key, $value);
            } else {
                $templateProcessor->setValue($key, str_replace("\n", '<w:br/>', $value ?? ''));
            }
        }
        return $templateProcessor;
    }

    /**
     * Helper function to process array values for tables in the template.
     */
    private function processArrayValue(TemplateProcessor $templateProcessor, string $key, array $value): void
    {
        if (empty($value) || !isset($value[0]) || !is_array($value[0])) return;

        // Grouped Table
        if (isset($value[0]['group_title'])) {
            $tabelFinal = [];
            $firstGroupColumn = key(array_filter($value[0], 'is_array'));
            foreach ($value as $group) {
                if (empty($group[$firstGroupColumn])) continue;
                for ($i = 0; $i < count($group[$firstGroupColumn]); $i++) {
                    $rowData = ['no' => count($tabelFinal) + 1, 'group_title' => $group['group_title']];
                    foreach ($group as $fieldKey => $fieldValue) {
                        if ($fieldKey !== 'group_title') {
                            $rowData[$fieldKey] = $fieldValue[$i] ?? '';
                        }
                    }
                    $tabelFinal[] = $rowData;
                }
            }
            if (!empty($tabelFinal)) {
                $templateProcessor->cloneRowAndSetValues('no', $tabelFinal);
            }
        // Simple Table
        } else {
            foreach ($value as $index => &$row) {
                $row['no'] = $index + 1;
            }
            $firstColumnKey = array_key_first($value[0]);
            if ($firstColumnKey) {
                $templateProcessor->cloneRowAndSetValues($firstColumnKey, $value);
            }
        }
    }
}