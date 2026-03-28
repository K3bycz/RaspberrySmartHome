<?php

namespace App\Http\Controllers;

use App\Models\TemperatureReading;
use App\Models\DistanceReading;
use Illuminate\Http\Request;

class SensorController extends Controller
{
    // Metoda do budowania widoku listy temperatury i wilgotności
    public function temperatureList()
    {
        $temperatures = TemperatureReading::orderBy('timestamp', 'desc')->get();

        return view('sensors.temperature_list', compact('temperatures'));
    }

    // Metoda do pobierania danych ajax dla wykresów i tabel temperatury i wilgotności
    public function getTemperatureData(Request $request)
    {
        $query = TemperatureReading::query();

        if ($request->filled('date_from')) {
            $query->where('timestamp', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('timestamp', '<=', $request->date_to . 'T23:59:59');
        }

        $data = $query->orderBy('timestamp', 'desc')->get();

        return response()->json([
            'total' => $data->count(),
            'data' => $data,
        ]);
    }

    // Metody do budowania widoku listy odczytów z czujników ruchu
    public function motionDetectionList()
    {
        $distances = DistanceReading::orderBy('timestamp', 'desc')->get();

        return view('sensors.motion_detection_list', compact('distances'));
    }

    // Metoda do pobierania danych ajax dla wykresu i tabeli odczytów z czujników ruchu
    public function getDistanceData(Request $request)
    {
        $query = DistanceReading::query();

        if ($request->filled('date_from')) {
            $query->where('timestamp', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('timestamp', '<=', $request->date_to . 'T23:59:59');
        }

        $data = $query->orderBy('timestamp', 'desc')->get();

        return response()->json([
            'total' => $data->count(),
            'data' => $data,
        ]);
    }
    
}