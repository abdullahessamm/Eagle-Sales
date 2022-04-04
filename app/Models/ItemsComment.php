<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemsComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'comment',
        'customer_id'
    ];
}
