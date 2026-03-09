<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class TemperatureReading extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'temperature_readings';

    protected $fillable = [
        'timestamp',
        'temperature',
        'humidity',
        'status',
    ];
}