<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class DistanceReading extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'distance_readings';

    protected $fillable = [
        'timestamp',
        'distance',
        'status',
    ];
}