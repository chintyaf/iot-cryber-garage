<?php

// app/Models/SensorLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorLog extends Model
{
    protected $table = 'sensor_logs'; // Contoh: 'sensor_logs'
    use HasFactory;

    protected $fillable = [
        'parking_distance',
        'gas_value',
        'status',
    ];

    /**
     * Scope untuk mengambil data 24 jam terakhir (untuk grafik Chart.js)
     */
    public function scopeRecent($query)
    {
        return $query->where('created_at', '>=', now()->subDay())
                     ->orderBy('created_at', 'asc');
    }
}
