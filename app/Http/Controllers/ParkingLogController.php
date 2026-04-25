<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ParkingLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ParkingLogController extends Controller
{
    /**
     * INDEX: Menyiapkan data untuk Grafik Bar Chart (Frekuensi per Hari)
     */
    // ParkingLogController.php
    public function index()
    {
        $logs = ParkingLog::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as total')
            )
            ->whereIn('event_type', ['MASUK', 'PARKIR'])
            ->where('created_at', '>=', Carbon::now()->subDays(6)) // 7 hari terakhir termasuk hari ini
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Format data untuk mempermudah JS
        $formatted = $logs->map(function($item) {
            return [
                'label' => Carbon::parse($item->date)->translatedFormat('D'), // Contoh: Sen, Sel, Rab
                'total' => $item->total
            ];
        });

        return response()->json($formatted);
    }

    /**
     * STORE: Menerima data event parkir dari ESP8266/NodeMCU
     */
    public function store(Request $request)
    {
        // 1. Validasi request dari mikrokontroler
        $validated = $request->validate([
            'event_type' => 'required|string|in:MASUK,KELUAR,PARKIR', // Pastikan input sesuai
        ]);

        // 2. Simpan ke database
        $parkingLog = ParkingLog::create([
            'event_type' => strtoupper($validated['event_type']) // Pastikan huruf kapital
        ]);

        // 3. (Opsional) Tambahkan juga ke system_logs untuk riwayat UI
        // \App\Models\SystemLog::create([
        //     'type'      => 'SENSOR',
        //     'parameter' => 'Sensor Parkir (IR/Ultrasonic)',
        //     'value'     => $parkingLog->event_type,
        //     'status'    => $parkingLog->event_type,
        // ]);

        // 4. Beri response sukses
        return response()->json([
            'status'  => 'success',
            'message' => 'Event parkir ' . $parkingLog->event_type . ' berhasil dicatat!',
            'data'    => $parkingLog
        ], 201);
    }
}
