<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // DB::table('device_states')->insert([
            // [
            //     'device_name' => 'exhaust-fan',
            //     'is_active'   => false,
            //     'mode'        => 'AUTO',
            //     'created_at'  => now(),
            //     'updated_at'  => now(),
            // ],
            // Kamu bisa tambah device lain di sini nanti
            /*
            [
                'device_name' => 'air-purifier',
                'is_active'   => true,
                'mode'        => 'MANUAL',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            */
        // ]);
    }
}
