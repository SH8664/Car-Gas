<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResortRequest extends Model
{
    use HasFactory;
    protected $fillable=[
        'name',
        'performance_num',
        'first_desire_id',
        'second_desire_id',
        'third_desire_id',
        'status',
        'relatives'

    ];
}
