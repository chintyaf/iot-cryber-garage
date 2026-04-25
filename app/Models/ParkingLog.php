<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkingLog extends Model
{
    use HasFactory;

    // Izinkan mass-assignment untuk event_type
    protected $fillable = [
        'event_type'
    ];
}
