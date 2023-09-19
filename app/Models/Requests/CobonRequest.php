<?php

namespace App\Models\Requests;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CobonRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'cobon_id',
        'user_id',
        'payment_way',
        'amount',
        'has_partner',
        'partner_id',
        'status'
    ];
}