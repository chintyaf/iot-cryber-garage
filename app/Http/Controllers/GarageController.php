<?php

namespace App\Http\Controllers;

use App\Models\SensorLog;
use Illuminate\Http\Request;

class GarageController extends Controller
{
// GarageController.php
    public function store(Request $request) {
        SensorLog::create([
            'distance'   => $request->distance,
            'gas'        => $request->gas,
            'fan_status' => $request->fan_status,
            'air_status' => $request->air_status,
        ]);
        return response()->json(['status' => 'ok']);
    }

    // public function getCommand() {
    //     $cmd = ManualCommand::latest()->first();
    //     return response()->json([
    //         'fan_on' => $cmd ? $cmd->fan_on : false
    //     ]);
    // }
}
