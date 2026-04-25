<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeviceState; // Pastikan model Device sudah di-import

class DeviceController extends Controller
{
    /**
     * 1. GET STATUS: Dipanggil oleh fungsi pollFanStatus() di JS
     * Mengambil status terbaru dari database
     */
    public function getStatus($name)
    {
        // Cari device berdasarkan kolom 'device_name' (misal: 'exhaust-fan')
        $device = DeviceState::where('device_name', $name)->first();

        // Jika device ditemukan
        if ($device) {
            return response()->json([
                // Kita cast (bool) agar kembaliannya true/false, bukan 1/0
                // Ini penting agar if (serverState !== fanOn) di JS kamu berjalan akurat
                'is_active' => (bool) $device->is_active
            ], 200);
        }

        // Jika tidak ada device dengan nama tersebut di database
        return response()->json([
            'error' => 'Device tidak ditemukan'
        ], 404);
    }

    /**
     * 2. MANUAL OVERRIDE: Dipanggil saat tombol ditekan (toggleFan)
     * Mengubah status di database
     */
    public function manualOverride(Request $request, $name)
    {
        $device = DeviceState::where('device_name', $name)->first();

        if ($device) {
            // Update kolom is_active sesuai request dari JS targetState
            $device->update([
                'is_active' => $request->is_active
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kipas berhasil di-override menjadi: ' . ($request->is_active ? 'ON' : 'OFF')
            ], 200);
        }

        return response()->json([
            'error' => 'Device tidak ditemukan'
        ], 404);
    }
}
