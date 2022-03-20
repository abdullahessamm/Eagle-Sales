<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DuesOfSeller extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'cash',
        'is_reward',
        'reward_type',
        'is_salary',
        'is_commission',
        'tax',
        'order_id',
        'was_withdrawn',
        'notes'
    ];
}
