<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemsRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'rate',
        'customer_id'
    ];
}
