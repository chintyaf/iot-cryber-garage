<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    use HasFactory;

    // Izinkan mass-assignment untuk keempat kolom ini
    protected $fillable = [
        'type',
        'parameter',
        'value',
        'status',
    ];
}
