<?php

namespace App\Models\Requests;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'hotel_id',
        'user_id',
        'dependents',
        'room',
    ];
}