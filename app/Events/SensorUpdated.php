<?php

namespace App\Events;

use App\Models\SensorLog; // Import model kamu
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast; // Pastikan ini di-import
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// 1. TAMBAHKAN 'implements ShouldBroadcast'
class SensorUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sensorLog; // Properti ini otomatis dikirim ke frontend

    /**
     * Create a new event instance.
     */
    // 2. TERIMA DATA DARI CONTROLLER
    public function __construct(SensorLog $sensorLog)
    {
        $this->sensorLog = $sensorLog;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        // 3. GANTI KE CHANNEL PUBLIK UNTUK TESTING
        return [
            new Channel('sensors'),
        ];
    }

    // Optional: Nama event yang bakal didengerin di React
    public function broadcastAs()
    {
        return 'SensorUpdated';
    }
}
