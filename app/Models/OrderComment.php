<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'comment',
        'author_id'
    ];

    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }
}
