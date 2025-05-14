<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use App\Models\JenisSurat;
use App\Models\HistorySurat;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\TemplateProcessor;
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


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
            $request->session()->regenerate(); // keamanan session
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
        // Cari jenis surat berdasarkan kode_jenis
        $jenisSurat = JenisSurat::where('kode_jenis', $jenis)->firstOrFail();

        // Simpan ke tabel surat
        $surat = Surat::create([
            'user_id' => Auth::id(),
            'email' => Auth::user()->email,
            'jenis_surat_id' => $jenisSurat->id,
            'nomor_surat' => null,
            'isi_data' => json_encode($request->except('_token')),
            'file_surat' => null,
        ]);

        // Catat ke tabel history_surat
        HistorySurat::create([
            'surat_id' => $surat->id,
            'user_id' => Auth::id(),
            'aksi' => 'buat',
            'waktu_aksi' => now(),
        ]);

        return redirect('/')->with('success', 'Surat berhasil disimpan.');
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
        $surat = Surat::with('jenisSurat')->findOrFail($id);
        $jenisSurat = $surat->jenisSurat;

        // Ambil template dan isi data
        $templatePath = storage_path('app/public/' . $jenisSurat->template_file);
        $isiData = json_decode($surat->isi_data, true);

        if (!file_exists($templatePath)) {
            abort(404, 'Template surat tidak ada.');
        }

        // Load dan proses template Word
        $templateProcessor = new TemplateProcessor($templatePath);
        foreach ($isiData as $key => $value) {
            $templateProcessor->setValue($key, $value);
        }

        // Simpan file hasil
        $filename = Str::slug($jenisSurat->nama_jenis) . '-' . $surat->id . '.' . $format;
        $savePath = storage_path('app/public/generated/' . $filename);

        if ($format === 'docx') {
            $templateProcessor->saveAs($savePath);
            return response()->download($savePath)->deleteFileAfterSend(true);
        } elseif ($format === 'pdf') {
            // Simpan sementara ke docx
            $tempDocx = storage_path('app/public/generated/temp-' . $surat->id . '.docx');
            $templateProcessor->saveAs($tempDocx);

            // Convert to HTML manually (PhpWord tidak mendukung PDF langsung dari TemplateProcessor)
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($tempDocx);
            $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');

            $htmlPath = storage_path("app/public/generated/temp-{$surat->id}.html");
            $xmlWriter->save($htmlPath);
            $html = file_get_contents($htmlPath);

            // Gunakan dompdf untuk konversi HTML ke PDF
            $pdf = PDF::loadHTML($html)->setPaper('A4', 'portrait');
            return $pdf->download(Str::slug($jenisSurat->nama_jenis) . '-' . $surat->id . '.pdf');
        }

        abort(400, 'Format tidak didukung.');
    }
}
