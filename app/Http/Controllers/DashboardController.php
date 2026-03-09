<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{   
    // metoda budująca widok dashboardu
    public function index()
    {
        return view('dashboards.dashboard');
    }
}