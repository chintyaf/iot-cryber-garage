<?php

use Illuminate\Support\Facades\Route;

// Import semua Controller yang digunakan
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\SensorLogController;
use App\Http\Controllers\ParkingLogController;
use App\Http\Controllers\SystemLogController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Di sinilah kamu mendaftarkan semua rute web untuk aplikasimu.
*/

// ---------------------------------------------------------
// 0. HALAMAN UTAMA (DASHBOARD)
// ---------------------------------------------------------
// Menampilkan file resources/views/dashboard.blade.php
Route::get('/', function () {
    return view('dashboard');
});

Route::get('/system-log', function () {
    return view('system-log');
})->name('system-log');
// ---------------------------------------------------------
// 1. KENDALI ALAT (Device Control - Kipas Exhaust)
// ---------------------------------------------------------
// Menerima perintah klik tombol dari Frontend (Fetch API)
Route::post('/device/{name}/override', [DeviceController::class, 'manualOverride']);

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
