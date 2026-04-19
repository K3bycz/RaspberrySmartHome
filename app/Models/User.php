<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class User extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'location_lat',
        'location_lon',
        'location_name',
    ];

    // Ukryj hasło przy serializacji do JSON/array
    protected $hidden = [
        'password',
    ];
}