<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SensorLog; // Pastikan Model sudah dibuat
use Carbon\Carbon;

class SensorLogController extends Controller
{
    /**
     * INDEX: Menampilkan data ke Dashboard (Frontend)
     */
    public function index()
    {
        // Mengambil data terbaru (misal: 24 jam terakhir untuk grafik gas)
        $grafikGas = SensorLog::where('created_at', '>=', Carbon::now()->subDay())
            ->orderBy('created_at', 'asc')
            ->get(['gas_value', 'created_at']);

        // Mengambil 1 data paling terakhir untuk panel "JARAK AKHIR" & "GAS TERKINI"
        $dataTerkini = SensorLog::latest()->first();

        // Mengambil data log terbaru untuk tabel riwayat (misal 6 entri terbaru)
        $riwayatLogs = SensorLog::latest()->take(6)->get();

        // Kirim ke view (misal nama view-nya 'dashboard.index')
        // return view('dashboard.index', compact('grafikGas', 'dataTerkini', 'riwayatLogs'));

        // ATAU jika kamu pakai API/Fetch JavaScript, kembalikan sebagai JSON:
        return response()->json([
            'grafik_gas' => $grafikGas,
            'terkini' => $dataTerkini,
            'riwayat' => $riwayatLogs
        ]);
    }

    /**
     * STORE: Menerima data dari ESP8266/NodeMCU dan menyimpannya ke database
     */
public function store(Request $request)
{
    // 1. Validasi
    $validated = $request->validate([
        'parking_distance' => 'nullable|numeric',
        'gas_value'        => 'nullable|numeric',
        'status'           => 'nullable|string|max:50',
    ]);

    // 2. Perbaikan Logika Status (Gunakan null coalescing atau default value)
    if (!isset($validated['status'])) {
        // Ambil gas_value, jika tidak dikirim beri default 0 agar tidak error saat dibandingkan
        $gasVal = $validated['gas_value'] ?? 0;

        if ($gasVal > 400) {
            $validated['status'] = 'Kritis';
        } else {
            $validated['status'] = 'Aman';
        }
    }

    // 3. Simpan data (Gunakan null coalescing operator ?? null)
    $log = SensorLog::create([
        'parking_distance' => $validated['parking_distance'] ?? null,
        'gas_value'        => $validated['gas_value'] ?? null,
        'status'           => $validated['status'] ?? 'Aman',
    ]);

    return response()->json([
        'status'  => 'success',
        'message' => 'Data sensor berhasil disimpan',
        'data'    => $log
    ], 201);
}
}
