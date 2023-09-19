<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelAllowance extends Model
{

    use HasFactory;
    protected $fillable = [
        'user_id',
        'start_date',
        'end_date',
        'days_count',
        'from',
        'to',
        'accommodation_type',
        'meals_count',
        'meals_cost',
        'transport_count',
        'transport_cost',
        'travel_cost',
        'total'
    ];
}
