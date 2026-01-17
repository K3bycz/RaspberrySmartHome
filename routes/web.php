<?php

use Illuminate\Support\Facades\Route;

// Dashboardy
Route::get('/', function () {
    return view('dashboards.dashboard');
})->name('dashboard');
