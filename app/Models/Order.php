<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    const STATUS_OPEN = 0;
    const STATUS_REQUEST_MORE_INFO = 1;
    const STATUS_CANCELED = 2;
    const STATUS_APPROVED = 3;
    const STATUS_REJECTED = 4;
    const STATUS_UNDER_SHIPPING = 5;
    const STATUS_DELIVERED = 6;

    protected $fillable = [
        'supplier_id',
        'customer_id',
        'state',
        'require_shipping',
        'shipping_address_id',
        'billing_address_id',
        'required',
        'tax',
        'discount',
        'total_required',
        'is_credit',
        'credit_limit',
        'deposit',
        'remaining',
        'created_by',
        'updated_by',
        'delivery_date',
        'delivered_at',
    ];

    protected $hidden = [
        'supplier_id',
        'customer_id',
        'shipping_address_id',
        'billing_address_id',
        'created_by',
        'updated_by',
    ];

    // get supplier
    public function getSupplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id')->first();
    }

    // get customer
    public function getCustomer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id')->first();
    }

    // get shipping address
    public function getShippingAddress()
    {
        return $this->belongsTo(UserPlace::class, 'shipping_address_id', 'id')->first();
    }

    // get billing address
    public function getBillingAddress()
    {
        return $this->belongsTo(UserPlace::class, 'billing_address_id', 'id')->first();
    }

    // get creator of order if exists
    public function getCreator()
    {
        return $this->belongsTo(Seller::class, 'created_by', 'id')->first();
    }

    // get updater of order if exists
    public function getUpdater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id')->first();
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany(OrderComment::class, 'order_id', 'id');
    }

    public function calculateCommission(float $commissionPercentage)
    {
        $commission = $this->total_required * $commissionPercentage / 100;
        return $commission;
    }
}