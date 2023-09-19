<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cobon extends Model
{
    use HasFactory;

    protected $fillable = [
        'resturant_name',
        'price',
        'type_of_price',
        'is_available'
    ];
}