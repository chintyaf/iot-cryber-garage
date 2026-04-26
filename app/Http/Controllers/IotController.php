<?php
namespace App\Http\Controllers;

use App\Models\ParkingLog;
use App\Models\SensorLog;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IotController extends Controller
{
    public function syncParking(Request $request)
    {
        // 1. Validate only the parking data
        $validated = $request->validate([
            'parking_distance' => 'required|numeric'
        ]);

        $parkingDistance = $validated['parking_distance'];
        $status = ($parkingDistance < 10) ? 'PARKIR' : 'KELUAR';

        try {
            // 2. Parking Event Log
            ParkingLog::create([
                'event_type' => $status
            ]);

            // 3. Sensor Historis (Gas value left null)
            SensorLog::create([
                'parking_distance' => $parkingDistance,
                'status' => 'NORMAL' // Default or based on distance
            ]);

            // 4. UI Dashboard Log
            SystemLog::create([
                'type' => 'SENSOR',
                'parameter' => 'Jarak Sensor',
                'value' => $parkingDistance . ' cm',
                'status' => $status
            ]);

             return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Parking Sync Error: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }

    public function syncGas(Request $request)
    {
        // 1. Validate only the gas data
        $validated = $request->validate([
            'gas_value' => 'required|numeric'
        ]);

        $gasValue = $validated['gas_value'];
        $status = ($gasValue > 400) ? 'TINGGI' : 'NORMAL';

        try {
            // 2. Sensor Historis (Parking distance left null)
            SensorLog::create([
                'gas_value' => $gasValue,
                'status' => $status
            ]);

            // 3. UI Dashboard Log
            SystemLog::create([
                'type' => 'GAS ALERT',
                'parameter' => 'Gas MQ-2',
                'value' => $gasValue . ' ppm',
                'status' => $status
            ]);

             return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Gas Sync Error: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }
}
