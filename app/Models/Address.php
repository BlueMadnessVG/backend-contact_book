<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [ 
        'type',
        'street',
        'number',
        'suburb',
        'city',
        'state',
        'postalCode',
        'country'
    ];
}
