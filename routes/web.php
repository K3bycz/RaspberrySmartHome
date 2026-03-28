<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SensorController;
use App\Http\Controllers\SettingsController;


// Dashboardy
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
// Czujniki
Route::get('/sensors/temperature/list', [SensorController::class, 'temperatureList'])->name('sensors.temperature.list');
Route::get('/sensors/motion/list', [SensorController::class, 'motionDetectionList'])->name('sensors.motion.list');

Route::get('/sensors/data/temperature', [SensorController::class, 'getTemperatureData'])->name('sensors.data.temperature');
Route::get('/sensors/data/distance', [SensorController::class, 'getDistanceData'])->name('sensors.data.distance');

// Ustawienia
Route::get('/settings/general', [SettingsController::class, 'generalSettings'])->name('settings.general');

?>