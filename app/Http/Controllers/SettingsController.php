<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SettingsController extends Controller
{   
    // Metoda do budowania widoku ustawień ogólnych
    public function generalSettings()
    {
        $user = User::find(session('user_id'));
        return view('settings.general_settings', compact('user'));
    }

    // Metoda do zapisywania lokalizacji użytkownika
    public function saveLocation(Request $request)
    {
        $request->validate([
            'location_lat'  => 'required|numeric|between:-90,90',
            'location_lon'  => 'required|numeric|between:-180,180',
            'location_name' => 'required|string|max:100',
        ]);

        $user = User::find(session('user_id'));

        if (!$user) {
            return redirect()->route('login');
        }

        $user->location_lat  = $request->location_lat;
        $user->location_lon  = $request->location_lon;
        $user->location_name = $request->location_name;
        $user->save();

        return redirect()->back()->with('success', 'Lokalizacja została zapisana.');
    }
}