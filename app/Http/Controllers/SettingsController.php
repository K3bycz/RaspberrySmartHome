<?php

namespace App\Http\Controllers;

class SettingsController extends Controller
{
    // Metoda do budowania widoku ustawień ogólnych
    public function generalSettings()
    {
        return view('settings.general_settings');
    }

}