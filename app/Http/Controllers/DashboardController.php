<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TemperatureReading;

class DashboardController extends Controller
{   
    // Metoda budująca widok dashboardu
    public function index()
    {   
        $latestReading = TemperatureReading::where('status', 'ok')
            ->orderBy('timestamp', 'desc')
            ->first();

        $user = User::find(session('user_id'));
        $location = [
            'lat'  => $user->location_lat  ?? null,
            'lon'  => $user->location_lon  ?? null,
            'name' => $user->location_name ?? null,
        ];

        return view('dashboards.dashboard', compact('latestReading', 'location'));
    }
}