<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingDue extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'supplier_id',
        'amount',
        'tax',
        'discount',
        'total_amount',
        'obtained',
        'obtained_at',
        'obtained_by',
    ];

    protected $hidden = [
        'order_id',
        'supplier_id',
        'obtained_by',
    ];

    protected $dates = [
        'obtained_at',
    ];

    public function getOrder()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id')->first();
    }

    public function getOrderAttribute()
    {
        return $this->getOrder();
    }

    public function getSupplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id')->first();
    }

    public function getSupplierAttribute()
    {
        return $this->getSupplier();
    }

    public function getObtainedBy()
    {
        return $this->belongsTo(User::class, 'obtained_by', 'id')->first();
    }

    public function getObtainedByAttribute()
    {
        return $this->getObtainedBy();
    }
}
