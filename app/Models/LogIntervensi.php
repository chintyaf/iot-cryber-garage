<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogIntervensi extends Model
{
    use HasFactory;

    // Override nama tabel bawaan Laravel
    protected $table = 'log_intervensi';

    protected $fillable = [
        'event_type',
        'message',
        'action_taken',
    ];
}
