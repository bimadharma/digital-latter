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
                if (isset($value[0]) && is_array($value[0])) {
                    $firstColumnKey = array_key_first($value[0]);
                    foreach ($value as $index => &$row) {
                        $row['no'] = $index + 1;
                    }
                    $templateProcessor->cloneRowAndSetValues($firstColumnKey, $value);
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
            'aksi' => 'buat',
            'waktu_aksi' => now(),
        ]);

        return redirect('/history')->with('success', 'Surat berhasil disimpan dan file berhasil dikonversi ke PDF.');
    }




    public function history()
    {
        $suratList = Surat::with('jenisSurat')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('pages.history', compact('suratList'));
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
}
