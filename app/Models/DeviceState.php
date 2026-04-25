<?php
// app/Models/DeviceState.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceState extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_name',
        'is_active',
        'mode',
    ];

    /**
     * Konversi tipe data otomatis saat ditarik dari database
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getRouteKeyName()
    {
        return 'name'; // Laravel akan mencari data berdasarkan kolom 'name' bukan 'id'
    }
}
