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
                'is_active' => (bool) $device->is_active,
                'mode' => $device->mode
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
    // 1. Validasi input: is_active (boolean) dan mode (harus AUTO atau MANUAL)
    $request->validate([
        'is_active' => 'required|boolean',
        'mode'      => 'required|in:AUTO,MANUAL' // Memastikan input hanya 2 pilihan ini
    ]);

    try {
        $device = DeviceState::where('device_name', $name)->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => "Device {$name} tidak ditemukan"
            ], 404);
        }

        // 2. Update status dan mode secara bersamaan
        $device->update([
            'is_active' => $request->is_active,
            'mode'      => strtoupper($request->mode) // Pastikan uppercase untuk konsistensi
        ]);

        return response()->json([
            'success' => true,
            'message' => "Update Berhasil!",
            'data' => [
                'device_name' => $name,
                'status'      => $device->is_active ? 'ON' : 'OFF',
                'mode'        => $device->mode
            ]
        ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    public function syncFanStatus(Request $request)
    {
        // 1. Validasi data yang masuk dari IoT
        $request->validate([
            'device_name' => 'required|string',
            'is_active'   => 'required|boolean',
        ]);

        try {
            // 2. Cari device di database berdasarkan nama
            $device = DeviceState::where('device_name', $request->device_name)->first();

            // 3. Jika device tidak ada di database, tolak request-nya
            if (!$device) {
                return response()->json([
                    'success' => false,
                    'message' => "Device '{$request->device_name}' tidak ditemukan. Update dibatalkan."
                ], 404);
            }

            // --- TAMBAHAN: CEK MODE ---
            // 4. Jika sedang mode MANUAL, abaikan update dari IoT
            if ($device->mode === 'MANUAL') {
                return response()->json([
                    'success' => true,
                    'message' => 'Update dari sensor diabaikan karena device sedang dalam mode MANUAL',
                    'current_mode' => $device->mode
                ], 200);
                // Kita tetap membalas 200 (OK) agar Arduino tidak mengira terjadi error server,
                // dan sekaligus memberi tahu Arduino bahwa mode saat ini adalah MANUAL.
            }

            // 5. Jika mode AUTO, lakukan update statusnya
            $device->update([
                'is_active' => $request->is_active
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status device berhasil diupdate dari sensor',
                'current_mode' => $device->mode
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
