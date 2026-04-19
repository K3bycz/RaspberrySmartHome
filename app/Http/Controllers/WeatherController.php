<?php
// app/Http/Controllers/WeatherController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{

    // metoda pobierająca aktualne dane pogodowe z Open-Meteo API
    public function current(Request $request)
    {
        $lat = $request->query('lat');
        $lon = $request->query('lon');

        if (!$lat || !$lon) {
            return response()->json(['error' => 'Brak lokalizacji. Ustaw ją w Ustawieniach.'], 422);
        }

        try {
            $response = Http::timeout(10)->get('https://api.open-meteo.com/v1/forecast', [
                'latitude'  => $lat,
                'longitude' => $lon,
                'current'   => 'temperature_2m,weather_code,wind_speed_10m,relative_humidity_2m',
                'timezone'  => 'auto',
            ]);

            if ($response->failed()) {
                return response()->json(['error' => 'Błąd API: ' . $response->status()], 500);
            }

            $data    = $response->json();
            $current = $data['current'] ?? [];
            $code    = isset($current['weather_code']) ? (int) $current['weather_code'] : 0;

            return response()->json([
                'temperature' => $current['temperature_2m']        ?? null,
                'humidity'    => $current['relative_humidity_2m']  ?? null,
                'windspeed'   => $current['wind_speed_10m']        ?? null,
                'weathercode' => $code,
                'description' => $this->weatherDescription($code),
                'icon'        => $this->weatherIcon($code),
                'debug_raw'   => $current, // tymczasowo - usuń po naprawieniu
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // metoda mapująca kody pogody na opisy tekstowe
    private function weatherDescription(int $code): string
    {
        return match(true) {
            $code === 0             => 'Bezchmurnie',
            in_array($code, [1,2])  => 'Częściowe zachmurzenie',
            $code === 3             => 'Pochmurno',
            in_array($code, [45,48])=> 'Mgła',
            in_array($code, [51,53,55]) => 'Mżawka',
            in_array($code, [61,63,65]) => 'Deszcz',
            in_array($code, [71,73,75]) => 'Śnieg',
            in_array($code, [80,81,82]) => 'Przelotne opady',
            in_array($code, [95,96,99]) => 'Burza',
            default                 => 'Brak danych',
        };
    }

    // metoda mapująca kody pogody na ikony Font Awesome
    private function weatherIcon(int $code): string
    {
        return match(true) {
            $code === 0             => 'fa-sun',
            in_array($code, [1,2])  => 'fa-cloud-sun',
            $code === 3             => 'fa-cloud',
            in_array($code, [45,48])=> 'fa-smog',
            in_array($code, [51,53,55,61,63,65,80,81,82]) => 'fa-cloud-rain',
            in_array($code, [71,73,75]) => 'fa-snowflake',
            in_array($code, [95,96,99]) => 'fa-bolt',
            default                 => 'fa-question',
        };
    }
}