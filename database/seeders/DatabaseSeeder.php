<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- 1. Seeder untuk Device States (Status Perangkat) ---
        DB::table('device_states')->insert([
            [
                'device_name' => 'exhaust-fan',
                'is_active'   => false,
                'mode'        => 'AUTO',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'device_name' => 'smart-lamp',
                'is_active'   => true,
                'mode'        => 'MANUAL',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);

        // --- 2. Seeder untuk Sensor Logs (Grafik Garis) ---
        // Membuat data simulasi selama 10 jam terakhir
        for ($i = 10; $i >= 0; $i--) {
            DB::table('sensor_logs')->insert([
                'parking_distance' => rand(50, 300), // cm
                'gas_value'        => rand(100, 600), // ppm
                'status'           => 'Normal',
                'created_at'       => Carbon::now()->subHours($i),
                'updated_at'       => Carbon::now()->subHours($i),
            ]);
        }

        // --- 3. Seeder untuk Parking Logs (Bar Chart Aktivitas) ---
        $events = ['MASUK', 'KELUAR', 'PARKIR'];
        for ($i = 0; $i < 20; $i++) {
            DB::table('parking_logs')->insert([
                'event_type' => $events[array_rand($events)],
                'created_at' => Carbon::now()->subDays(rand(0, 7)),
                'updated_at' => Carbon::now(),
            ]);
        }

        // --- 4. Seeder untuk System Logs (Log Detail/Tabel UI) ---
        DB::table('system_logs')->insert([
            [
                'type' => 'SENSOR',
                'parameter' => 'Jarak Sensor',
                'value' => '160 cm',
                'status' => 'PARKIR',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'SENSOR',
                'parameter' => 'Gas MQ-2',
                'value' => '342 ppm',
                'status' => 'TINGGI',
                'created_at' => now()->subMinutes(5),
                'updated_at' => now()->subMinutes(5),
            ],
            [
                'type' => 'ACTION',
                'parameter' => 'Exhaust Fan',
                'value' => 'ON',
                'status' => 'AUTO',
                'created_at' => now()->subMinutes(4),
                'updated_at' => now()->subMinutes(4),
            ],
            [
                'type' => 'ALERT',
                'parameter' => 'Kualitas Udara',
                'value' => 'Buruk',
                'status' => 'CRITICAL',
                'created_at' => now()->subMinutes(10),
                'updated_at' => now()->subMinutes(10),
            ],
        ]);
    }
}
