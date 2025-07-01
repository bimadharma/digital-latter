<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use App\Models\JenisSurat;
use Illuminate\Support\Str;
use App\Models\HistorySurat;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;


class SuratController extends Controller
{
    public function index(Request $request)
    {
        $jumlahSurat = Surat::count();
        $jenisSurat = JenisSurat::count();

        // Ambil keyword pencarian
        $search = $request->input('search');

        // Ambil dan filter data jenis surat
        $dataJenis = JenisSurat::all();
        $filtered = $dataJenis->filter(function ($item) use ($search) {
            return !$search || stripos($item->nama_jenis, $search) !== false;
        });

        // Bangun array cards dari hasil filtered
        $cards = $filtered->map(function ($jenis) {
            return [
                'title' => $jenis->nama_jenis,
                'desc' => $jenis->deskripsi,
                'href' => '/create/' . $jenis->kode_jenis,
                'template_url' => asset('storage/' . $jenis->template_file),
            ];
        });

        return view('pages.index', [
            'jumlahSurat' => $jumlahSurat,
            'jenisSurat' => $jenisSurat,
            'cards' => $cards,
        ]);
    }


    public function showLoginForm()
    {
        return view('pages.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('pages.index');
        }

        return back()->with('error', 'email atau Password salah');
    }

    // Fungsi untuk menampilkan form surat berdasarkan jenis
    public function buatSurat($jenis)
    {
        $jenisSurat = JenisSurat::where('kode_jenis', $jenis)->first();

        if (!$jenisSurat) {
            abort(404, 'Jenis surat tidak ditemukan.');
        }

        $templateFields = $jenisSurat->template_fields;

        return view("pages.form.form-laporan", compact('templateFields', 'jenisSurat', 'jenis'));
    }



    public function submitLaporanEUC(Request $request, $jenis)
    {
        $jenisSurat = JenisSurat::where('kode_jenis', $jenis)->firstOrFail();
        $isiData = $request->except('_token');

        // Inisialisasi template
        $templatePath = storage_path('app/public/' . $jenisSurat->template_file);
        if (!file_exists($templatePath)) {
            abort(404, 'Template surat tidak ditemukan.');
        }

        $templateProcessor = new TemplateProcessor($templatePath);

        foreach ($isiData as $key => $value) {
            if ($request->hasFile($key)) {
                $path = $request->file($key)->store('signatures', 'public');
                $fullPath = storage_path('app/public/' . $path);

                $templateProcessor->setImageValue($key, [
                    'path' => $fullPath,
                    'width' => 170,
                    'height' => 113,
                    'ratio' => true,
                ]);

                // Update isiData agar path signature tersimpan di database
                $isiData[$key] = $path;
            } elseif (is_array($value)) {
                // Cek jika array berisi tabel biasa
                if (isset($value[0]) && is_array($value[0]) && !isset($value[0]['group_title'])) {
                    $firstColumnKey = array_key_first($value[0]);
                    foreach ($value as $index => &$row) {
                        $row['no'] = $index + 1;
                    }
                    $templateProcessor->cloneRowAndSetValues($firstColumnKey, $value);

                    // Cek jika grouped_table
                } elseif (isset($value[0]['group_title']) && is_array($value[0])) {
                    $tabelFinal = [];

                    foreach ($value as $group) {
                        $rowCount = count($group['test_script']);

                        for ($i = 0; $i < $rowCount; $i++) {
                            $rowData = [
                                'no' => count($tabelFinal) + 1,
                                'group_title' => $group['group_title'],
                            ];

                            foreach ($group as $fieldKey => $fieldValue) {
                                if ($fieldKey === 'group_title') continue;
                                $rowData[$fieldKey] = $fieldValue[$i] ?? '';
                            }

                            $tabelFinal[] = $rowData;
                        }
                    }

                    $firstColumnKey = 'no';
                    $templateProcessor->cloneRowAndSetValues($firstColumnKey, $tabelFinal);
                }
            } else {
                $templateProcessor->setValue($key, str_replace("\n", '<w:br/>', $value));
            }
        }

        // ENCODE di sini setelah semua file/array ditangani
        $encodedIsiData = json_encode($isiData);

        // Buat nama file
        $slugNama = Str::slug($jenisSurat->nama_jenis);
        $timestamp = time();
        $docxName = "{$slugNama}-{$timestamp}.docx";
        $pdfName = "{$slugNama}-{$timestamp}.pdf";

        $docxPath = storage_path("app/public/generated/{$docxName}");
        $pdfPath = storage_path("app/public/generated/{$pdfName}");

        // Simpan DOCX
        $templateProcessor->saveAs($docxPath);

        // Konversi ke PDF dengan LibreOffice CLI
        $command = '"C:\\Program Files\\LibreOffice\\program\\soffice.exe" --headless --convert-to pdf --outdir ' . escapeshellarg(dirname($pdfPath)) . ' ' . escapeshellarg($docxPath);
        exec($command, $output, $resultCode);

        if ($resultCode !== 0 || !file_exists($pdfPath)) {
            return back()->with('error', 'Gagal mengonversi DOCX ke PDF.');
        }

        // Buat nomor surat
        $jumlahSurat = Surat::count();
        $newNumber = str_pad($jumlahSurat + 1, 2, '0', STR_PAD_LEFT);
        $nomorSurat = "SURAT-EXIM-{$newNumber}";

        // Simpan ke database
        $surat = Surat::create([
            'user_id' => Auth::id(),
            'email' => Auth::user()->email,
            'jenis_surat_id' => $jenisSurat->id,
            'nomor_surat' => $nomorSurat,
            'isi_data' => $encodedIsiData,
            'file_docx' => "generated/{$docxName}",
            'file_pdf' => "generated/{$pdfName}",
        ]);

        HistorySurat::create([
            'surat_id' => $surat->id,
            'user_id' => Auth::id(),
            'aksi' => $request->input('nama_surat', $jenisSurat->nama_jenis),
            'waktu_aksi'  => $surat->updated_at,
        ]);

        return redirect('/history')->with('success', 'Surat berhasil disimpan dan file berhasil dikonversi ke PDF.');
    }




    public function history()
    {
        $historyList = HistorySurat::with(['surat.jenisSurat'])
            ->where('user_id', Auth::id())
            ->latest('waktu_aksi')
            ->get();

        return view('pages.history', compact('historyList'));
    }


    public function cetakSurat(Request $request, $id)
    {
        $format = $request->query('format', 'pdf');
        $surat = Surat::findOrFail($id);

        if ($format === 'docx' && $surat->file_docx) {
            $path = storage_path("app/public/{$surat->file_docx}");
            if (file_exists($path)) {
                return response()->download($path);
            }
        } elseif ($format === 'pdf' && $surat->file_pdf) {
            $path = storage_path("app/public/{$surat->file_pdf}");
            if (file_exists($path)) {
                return response()->download($path);
            }
        }

        abort(404, 'File tidak ditemukan.');
    }

    public function edit($id)
    {
        $surat = Surat::with('jenisSurat')->findOrFail($id);
        $jenis = $surat->jenisSurat->kode_jenis;
        $templateFields = $surat->jenisSurat->template_fields;
        $isiData = json_decode($surat->isi_data, true);
        $jenisSurat = $surat->jenisSurat;

        // Ambil baris pertama dari HistorySurat berdasarkan surat_id
        $history = HistorySurat::where('surat_id', $id)->first();
        $aksi = $history ? $history->aksi : null;

        return view('pages.form.form-edit', compact('surat', 'jenis', 'templateFields', 'isiData', 'jenisSurat', 'aksi'));
    }

    public function update(Request $request, $id)
    {
        $surat = Surat::with('jenisSurat')->findOrFail($id);
        $jenisSurat = $surat->jenisSurat;

        // Data lama
        $oldIsiData = json_decode($surat->isi_data, true);
        $templateFields = $jenisSurat->template_fields;

        // Kumpulkan field signature
        $signatureFields = [];
        foreach ($templateFields as $section) {
            foreach ($section['fields'] as $field) {
                if ($field['field_type'] === 'signature') {
                    $signatureFields[] = $field['field_name'];
                }
            }
        }

        // Ambil input
        $isiData = $request->except(['_token', '_method', 'aksi']);

        // Tangani file upload baru
        foreach ($signatureFields as $field) {
            if ($request->hasFile($field)) {
                $filePath = $request->file($field)->store('signatures', 'public');
                $isiData[$field] = $filePath;
            } elseif (isset($oldIsiData[$field])) {
                $isiData[$field] = $oldIsiData[$field];
            }
        }

        // 1. Siapkan template processor
        $templatePath = storage_path('app/public/' . $jenisSurat->template_file);
        if (!file_exists($templatePath)) {
            return back()->with('error', 'Template tidak ditemukan.');
        }

        $templateProcessor = new TemplateProcessor($templatePath);

        // 2. Isi template dengan data yang diperbarui
        foreach ($isiData as $key => $value) {
            if (is_array($value)) {
                if (isset($value[0]) && is_array($value[0]) && !isset($value[0]['group_title'])) {
                    $firstColumnKey = array_key_first($value[0]);
                    foreach ($value as $index => &$row) {
                        $row['no'] = $index + 1;
                    }
                    $templateProcessor->cloneRowAndSetValues($firstColumnKey, $value);
                } elseif (isset($value[0]['group_title'])) {
                    $tabelFinal = [];
                    foreach ($value as $group) {
                        for ($i = 0; $i < count($group['test_script']); $i++) {
                            $rowData = [
                                'no' => count($tabelFinal) + 1,
                                'group_title' => $group['group_title'],
                            ];
                            foreach ($group as $fieldKey => $fieldValue) {
                                if ($fieldKey !== 'group_title') {
                                    $rowData[$fieldKey] = $fieldValue[$i] ?? '';
                                }
                            }
                            $tabelFinal[] = $rowData;
                        }
                    }
                    $templateProcessor->cloneRowAndSetValues('no', $tabelFinal);
                }
            } elseif (in_array($key, $signatureFields)) {
                $imagePath = storage_path('app/public/' . $value);
                if (file_exists($imagePath)) {
                    $templateProcessor->setImageValue($key, [
                        'path' => $imagePath,
                        'width' => 170,
                        'height' => 113,
                        'ratio' => true,
                    ]);
                }
            } else {
                $templateProcessor->setValue($key, str_replace("\n", '<w:br/>', $value));
            }
        }

        // 3. Simpan file dengan nama yang sama seperti sebelumnya
        $docxPath = storage_path('app/public/' . $surat->file_docx);
        $pdfPath = storage_path('app/public/' . $surat->file_pdf);

        // Simpan ulang DOCX
        $templateProcessor->saveAs($docxPath);

        // Konversi ulang ke PDF
        $command = '"C:\\Program Files\\LibreOffice\\program\\soffice.exe" --headless --convert-to pdf --outdir '
            . escapeshellarg(dirname($pdfPath)) . ' '
            . escapeshellarg($docxPath);
        exec($command, $output, $resultCode);

        // 4. Update DB
        $surat->update([
            'isi_data' => json_encode($isiData),
        ]);

        // Update history
        HistorySurat::updateOrCreate(
            ['surat_id' => $surat->id],
            [
                'aksi' => $request->input('aksi'),
                'waktu_aksi' => $surat->updated_at,
                'user_id' => Auth::id(),
            ]
        );

        return redirect('/history')->with('success', 'Surat dan file berhasil diperbarui.');
    }
}
