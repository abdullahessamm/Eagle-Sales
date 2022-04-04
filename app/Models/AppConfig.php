<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppConfig extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'key', 'value', 'updated_by', 'updated_at'
    ];

    protected $dates = [
        'updated_at'
    ];
}
