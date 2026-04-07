<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{   
    // Metoda budująca widok dashboardu
    public function index()
    {
        return view('dashboards.dashboard');
    }
}