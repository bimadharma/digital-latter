<?php

namespace App\Http\Controllers;

use App\Models\HistorySurat;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    /**
     * Display the user's letter history.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $historyList = HistorySurat::with(['surat.jenisSurat'])
            ->where('user_id', Auth::id())
            ->latest('waktu_aksi')
            ->get();

        return view('pages.history', compact('historyList'));
    }
}