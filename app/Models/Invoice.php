<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'serial_number',
        'buyer_id'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'invoice_id', 'id');
    }

    public function addOrders($orders)
    {
        return $this->orders()->saveMany($orders);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id', 'id');
    }

    public function generateSerialCode()
    {
        return $this->serial_number = now()->getTimestamp() . random_int(1000, 9999);
    }

    public function getTotalRequiredAttribute()
    {
        return $this->orders()->get()->sum('total_required');
    }
}
