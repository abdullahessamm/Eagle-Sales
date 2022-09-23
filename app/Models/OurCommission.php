<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OurCommission extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'supplier_id',
        'amount',
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

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    public function obtainedBy()
    {
        return $this->belongsTo(User::class, 'obtained_by', 'id');
    }
}
