<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabel untuk grafik Kualitas Udara (Gas Value) & Jarak Akhir
        Schema::create('sensor_logs', function (Blueprint $table) {
            $table->id();
            $table->float('parking_distance', 10, 2)->nullable(); // Jarak mobil
            $table->float('gas_value', 10, 2)->nullable();        // Nilai gas (PPM)
            $table->string('status', 50)->nullable();             // Status (Aman/Kritis)
            $table->timestamps();
        });

        // 2. Tabel untuk grafik Aktivitas Parkir (Frekuensi Bar Chart)
        Schema::create('parking_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event_type'); // Contoh: 'MASUK', 'KELUAR', 'PARKIR'
            $table->timestamps();
        });

        // 3. Tabel untuk Panel Kendali Manual (Exhaust Fan State)
        Schema::create('device_states', function (Blueprint $table) {
            $table->id();
            $table->string('device_name')->unique(); // ex: 'exhaust_fan'
            $table->boolean('is_active');            // true (ON) / false (OFF)
            $table->string('mode')->default('AUTO'); // 'AUTO' / 'MANUAL'
            $table->timestamps();
        });

        // 4. Tabel BARU untuk menyesuaikan panel "Record Sensor & Actuator Logs" di UI
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50);       // 'SENSOR', 'ACTION', 'ALERT'
            $table->string('parameter', 100); // 'Jarak Sensor', 'Gas MQ-2', 'Exhaust Fan'
            $table->string('value', 50);      // '160 cm', '342 ppm', 'ON', 'OFF'
            $table->string('status', 50);     // 'PARKIR', 'NORMAL', 'AUTO', 'TINGGI', 'MASUK'
            $table->timestamps();             // Digunakan untuk kolom TIMESTAMP di UI
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // PERBAIKAN: Hapus semua tabel dengan urutan terbalik dari pembuatannya
        Schema::dropIfExists('system_logs');
        Schema::dropIfExists('device_states');
        Schema::dropIfExists('parking_logs');
        Schema::dropIfExists('sensor_logs');
    }
};
