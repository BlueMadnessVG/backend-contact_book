<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $table = "contact";
    
    protected $fillable = [
        "name",
        "lastName",
        "email"
    ];

    public function address() {
        return $this->hasMany(Address::class);
    }

    public function phone() {
        return $this->hasMany(Phone::class);
    }
}
