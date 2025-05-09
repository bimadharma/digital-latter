<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use App\Models\JenisSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\HistorySurat;


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
        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate(); // keamanan session
            return redirect()->route('pages.index');
        }

        return back()->with('error', 'Username atau Password salah');
    }

    // Fungsi untuk menampilkan form surat berdasarkan jenis
    public function buatSurat($jenis)
    {
        // Ambil jenis surat berdasarkan kode_jenis
        $jenisSurat = JenisSurat::where('kode_jenis', $jenis)->first();

        if (!$jenisSurat) {
            abort(404, 'Jenis surat tidak ditemukan.');
        }

        // Ambil template_fields dalam bentuk array
        $templateFields = json_decode($jenisSurat->template_fields, true);

        // Kirim data template_fields ke view
        return view("pages.form.form-laporan", compact('templateFields', 'jenis'));
    }


    public function submitLaporanEUC(Request $request, $jenis)
    {
        // Cari jenis surat berdasarkan kode_jenis
        $jenisSurat = JenisSurat::where('kode_jenis', $jenis)->firstOrFail();

        // Simpan ke tabel surat
        $surat = Surat::create([
            'user_id' => Auth::id(),
            'username' => Auth::user()->username,
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
}
