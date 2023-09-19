<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School_money extends Model
{
    use HasFactory;
    protected $fillable = [
        'employee_name',
        'performance_num',
        'child_name',
        'child_BD',
        'relative_exists',
        'performance_num_relative',
        'child_attachements',
        'status',
        'within_age'
    ];
}
