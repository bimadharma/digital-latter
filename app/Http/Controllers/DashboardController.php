<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use App\Models\JenisSurat;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the application dashboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $jumlahSurat = Surat::count();
        $jenisSurat = JenisSurat::count();

        // Get search keyword
        $search = $request->input('search');

        // Filter letter types based on search
        $dataJenis = JenisSurat::query()
            ->when($search, function ($query, $search) {
                return $query->where('nama_jenis', 'like', "%{$search}%");
            })
            ->get();

        // Build cards array from the filtered result
        $cards = $dataJenis->map(function ($jenis) {
            return [
                'title' => $jenis->nama_jenis,
                'desc' => $jenis->deskripsi,
                'href' => route('surat.create', ['jenis' => $jenis->kode_jenis]),
                'template_url' => asset('storage/' . $jenis->template_file),
            ];
        });

        return view('pages.index', [
            'jumlahSurat' => $jumlahSurat,
            'jenisSurat' => $jenisSurat,
            'cards' => $cards,
        ]);
    }
}