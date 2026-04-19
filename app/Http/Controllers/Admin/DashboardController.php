<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelas;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_siswa' => Siswa::where('status', 'aktif')->count(),
            'total_guru'  => Guru::count(),
            'total_kelas' => Kelas::where('tahun_ajaran', '2024/2025')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}