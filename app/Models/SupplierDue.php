<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierDue extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'order_id',
        'amount',
        'withdrawn_at',
        'notes'
    ];

    protected $dates = [
        'withdrawn_at',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function loadCurrency()
    {
        $this->currency = $this->supplier()->first()->currency;
        return $this->currency;
    }

    public function getCurrencyAttribute()
    {
        return $this->loadCurrency();
    }
}
