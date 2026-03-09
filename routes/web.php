<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SensorController;

// Dashboardy
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
// Czujniki
Route::get('/sensors/list', [SensorController::class, 'list'])->name('sensors.list');

?>