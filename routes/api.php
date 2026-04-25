<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\SensorLogController;
use App\Http\Controllers\ParkingLogController;
use App\Http\Controllers\SystemLogController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/panel/sensor-logs', [DashboardController::class, 'getSensorLogs']);

// ---------------------------------------------------------
// 1. KENDALI ALAT (Device Control - Kipas Exhaust)
// ---------------------------------------------------------
// Menerima perintah klik tombol dari Frontend (Fetch API)
Route::post('/device/{name}/override', [DeviceController::class, 'manualOverride']);
Route::get('/device/{name}/status', [DeviceController::class, 'getStatus']);

// ---------------------------------------------------------
// 2. KUALITAS UDARA & JARAK (Sensor Logs)
// ---------------------------------------------------------
// Mengambil data untuk grafik Chart.js di Dashboard
Route::get('/sensor-logs', [SensorLogController::class, 'index']);
// Menerima data sensor yang dikirim dari NodeMCU/ESP8266
Route::post('/sensor-logs', [SensorLogController::class, 'store']);

// ---------------------------------------------------------
// 3. AKTIVITAS PARKIR (Parking Logs)
// ---------------------------------------------------------
// Mengambil data untuk grafik Bar Chart (Senin-Minggu)
Route::get('/parking-logs', [ParkingLogController::class, 'index']);
// Menerima trigger data parkir (MASUK/KELUAR) dari NodeMCU/ESP8266
Route::post('/parking-logs', [ParkingLogController::class, 'store']);

// ---------------------------------------------------------
// 4. RIWAYAT SISTEM (System Logs)
// ---------------------------------------------------------
// Mengambil 6 entri terbaru untuk tabel di UI Dashboard
Route::get('/system-logs', [SystemLogController::class, 'index']);
// (Opsional) Menerima log baru dari alat eksternal
Route::post('/system-logs', [SystemLogController::class, 'store']);


// // ---------------------------------------------------------
// // 5. TAMBAHAN UTS: THINGSPEAK & LOG INTERVENSI MYSQL
// // ---------------------------------------------------------

// // Endpoint untuk menerima data Log Intervensi & menyimpannya ke MySQL (Sesuai Tabel di PDF)
// Route::post('/log-intervensi', [SystemLogController::class, 'storeIntervensi']);

// // Endpoint untuk NodeMCU menerima data/perintah intervensi dari aplikasi Web (Mode Manual/Auto)
// Route::get('/device/command', [DeviceController::class, 'getCommand']);
