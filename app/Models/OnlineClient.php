<?php

namespace App\Models;

use App\Models\Traits\UsersTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlineClient extends Model
{
    use HasFactory, UsersTrait;

    protected $fillable = [
        'user_id',
        'l1_address',
        'l2_address',
        'location_coords',
        'created_at',
        'updated_at'
    ];

    protected $hidden = [
        'user_id',
        'created_at',
        'updated_at'
    ];
}
