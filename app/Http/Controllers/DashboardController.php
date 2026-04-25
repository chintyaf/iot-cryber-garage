<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
use App\Models\SensorLog; // Ganti dengan nama Model yang kamu gunakan untuk tabel sensor

class DashboardController extends Controller
{
    // Fungsi ini biasanya untuk menampilkan halaman web/view dashboard
    public function index()
    {
        return view('dashboard'); // Sesuaikan dengan nama view kamu
    }

    // Fungsi baru untuk dipanggil oleh fetch() di JavaScript
    public function getSensorLogs()
    {
        // Mengambil 1 data paling baru (mengurutkan berdasarkan created_at descending)
        $latestData = SensorLog::latest()->first();

        // Jika data ditemukan
        if ($latestData) {
            return response()->json([
                'terkini' => [
                    'parking_distance' => $latestData->parking_distance, // Pastikan nama kolom DB sesuai
                    'gas_value' => $latestData->gas_value              // Pastikan nama kolom DB sesuai
                ]
            ]);
        }

        // Jika tabel database masih kosong
        return response()->json([
            'terkini' => null,
            'message' => 'Data sensor belum tersedia'
        ], 404);
    }
}
