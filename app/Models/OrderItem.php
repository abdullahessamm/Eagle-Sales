<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'order_id',
        'promotion_id',
        'uom_id',
        'variant',
        'quantity',
        'total_before_discount',
        'discount',
        'total',
        'free_elements'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class, 'promotion_id', 'id');
    }

    public function uom()
    {
        return $this->belongsTo(Uom::class, 'uom_id', 'id');
    }
}
