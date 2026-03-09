<?php

namespace App\Http\Controllers;

use App\Models\TemperatureReading;
use App\Models\DistanceReading;

class SensorController extends Controller
{
    // metoda budujca widok z danymi z czujników
    public function list()
    {
        $temperatures = TemperatureReading::orderBy('timestamp', 'desc')->get();
        $distances = DistanceReading::orderBy('timestamp', 'desc')->get();

        return view('sensors.list', compact('temperatures', 'distances'));
    }

    
}