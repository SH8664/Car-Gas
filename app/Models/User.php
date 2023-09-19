<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    /*
            $table->integer('performance_num');
            $table->string('phone');
            $table->string('administration');
            $table->string('region');
            $table->string('title');
            $table->string('social_status');
            $table->boolean('isadmin'); */
            /*
            'performance_num',
        'phone',
        'administration',
        'region',
        'title',
        'social_status',
        'isadmin' */
    protected $fillable = [
        'name',
        'email',
        'password',
        'performance_num',
        'phone',
        'whatsapp_phone',
        'administration',
        'region',
        'title',
        'social_status',
        'limited_cobons',
        'isadmin'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
