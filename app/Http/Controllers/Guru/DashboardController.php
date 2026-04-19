<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $guru = Auth::user()->guru;

        // Kelas yang diajar guru ini
        $kelasDiajar = $guru ? Kelas::whereHas('jadwalPelajaran', fn($q) =>
            $q->where('guru_id', $guru->id)
        )->get() : collect();

        return view('guru.dashboard', compact('kelasDiajar'));
    }
}