<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemLog;

class SystemLogController extends Controller
{
    /**
     * INDEX: Mengambil log terbaru untuk tabel di UI Dashboard
     */
    public function index()
    {
        // Mengambil 6 entri paling baru berdasarkan waktu dibuat (created_at DESC)
        $logs = SystemLog::latest()->take(6)->get();

        // Mengembalikan data dalam bentuk JSON agar mudah ditangkap oleh JavaScript (Fetch API)
        return response()->json([
            'message' => 'Berhasil mengambil riwayat log sistem',
            'data' => $logs
        ]);

        // Catatan: Jika kamu merender langsung pakai Blade, gunakan ini:
        // return view('dashboard', compact('logs'));
    }

    /**
     * STORE: Menyimpan log baru (Dari sensor, aktuator, atau alert sistem)
     */
    public function store(Request $request)
    {
        // 1. Validasi input
        $validated = $request->validate([
            'type'      => 'required|string|max:50',       // contoh: 'SENSOR', 'ACTION', 'ALERT'
            'parameter' => 'required|string|max:100',      // contoh: 'Jarak Sensor', 'Gas MQ-2', 'Exhaust Fan'
            'value'     => 'required|string|max:50',       // contoh: '160 cm', '342 ppm', 'ON', 'OFF'
            'status'    => 'required|string|max:50',       // contoh: 'PARKIR', 'NORMAL', 'AUTO', 'TINGGI'
        ]);

        // 2. Simpan ke database
        $systemLog = SystemLog::create($validated);

        // 3. Beri response sukses
        return response()->json([
            'status'  => 'success',
            'message' => 'Log sistem berhasil dicatat!',
            'data'    => $systemLog
        ], 201);
    }
}
