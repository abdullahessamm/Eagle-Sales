<?php

namespace App\Models\Errors;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternalError extends Model
{
    use HasFactory;

    protected $fillable = [
        'platform', // "0" backend app, "1" frontend app, "2" mobile app
        'device_name', // server, js app, mobile app
        'type',
        'code',
        'message'
    ];

    
}
