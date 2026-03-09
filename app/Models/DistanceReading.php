<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class DistanceReading extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'hcsr04';

    protected $fillable = [
        'timestamp',
        'distance',
        'status',
    ];
}