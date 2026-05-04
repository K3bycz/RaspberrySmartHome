<?php
// app/Http/Controllers/BulbApiController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BulbApiController extends Controller
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(env('BULB_API_URL', 'http://localhost'), '/');
    }

    // GET /bulbs/status
    // zwraca stan obu żarówek
    public function status()
    {
        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/bulbs/status");

            if ($response->failed()) {
                return response()->json(['error' => 'Brak odpowiedzi od żarówek.'], 503);
            }

            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Brak połączenia z urządzeniem.'], 503);
        }
    }

    // POST /bulbs/on  — body: {"bulb": "bulb1"|"bulb2"|"all"}
    public function on(Request $request)
    {
        $request->validate(['bulb' => 'required|in:bulb1,bulb2,all']);

        return $this->proxyPost('/bulbs/on', ['bulb' => $request->bulb]);
    }

    // POST /bulbs/off — body: {"bulb": "bulb1"|"bulb2"|"all"}
    public function off(Request $request)
    {
        $request->validate(['bulb' => 'required|in:bulb1,bulb2,all']);

        return $this->proxyPost('/bulbs/off', ['bulb' => $request->bulb]);
    }

    // POST /bulbs/color — body: {"bulb": "bulb1"|"bulb2", "r": 255, "g": 0, "b": 0}
    public function color(Request $request)
    {
        $request->validate([
            'bulb' => 'required|in:bulb1,bulb2',
            'r'    => 'required|integer|min:0|max:255',
            'g'    => 'required|integer|min:0|max:255',
            'b'    => 'required|integer|min:0|max:255',
        ]);

        return $this->proxyPost('/bulbs/color', $request->only(['bulb', 'r', 'g', 'b']));
    }

    // POST /bulbs/brightness — body: {"bulb": "bulb1"|"bulb2"|"all", "brightness": 80}
    public function brightness(Request $request)
    {
        $request->validate([
            'bulb'       => 'required|in:bulb1,bulb2,all',
            'brightness' => 'required|integer|min:0|max:100',
        ]);

        return $this->proxyPost('/bulbs/brightness', $request->only(['bulb', 'brightness']));
    }

    // POST /bulbs/white — body: {"bulb": "all", "brightness": 100, "temperature": 50}
    public function white(Request $request)
    {
        $request->validate([
            'bulb'        => 'required|in:bulb1,bulb2,all',
            'brightness'  => 'required|integer|min:0|max:100',
            'temperature' => 'required|integer|min:0|max:100',
        ]);

        return $this->proxyPost('/bulbs/white', $request->only(['bulb', 'brightness', 'temperature']));
    }

    // Pomocnicza metoda - wysyła POST do API żarówek
    private function proxyPost(string $endpoint, array $data)
    {
        try {
            $response = Http::timeout(5)->post("{$this->baseUrl}{$endpoint}", $data);

            if ($response->failed()) {
                return response()->json(['error' => 'Błąd urządzenia: ' . $response->status()], 500);
            }

            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Brak połączenia z urządzeniem.'], 503);
        }
    }
}