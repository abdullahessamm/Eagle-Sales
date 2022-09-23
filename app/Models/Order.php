<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    const STATUS_OPEN = 0;
    const STATUS_REQUEST_MORE_INFO = 1;
    const STATUS_CANCELLED = 2;
    const STATUS_APPROVED = 3;
    const STATUS_REJECTED = 4;
    const STATUS_UNDER_SHIPPING = 5;
    const STATUS_DELIVERED = 6;

    protected $fillable = [
        'invoice_id',
        'supplier_id',
        'buyer_id',
        'state',
        'require_shipping',
        'shipping_address_id',
        'billing_address_id',
        'required',
        'tax',
        'discount',
        'total_required',
        'currency',
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
        'shipping_address_id',
        'billing_address_id',
        'credit_limit',
        'deposit',
        'remaining',
        'created_by',
        'updated_by',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, "invoice_id", "id");
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id', 'id');
    }

    // get supplier
    public function getSupplier()
    {
        return $this->supplier()->first();
    }

    public function shippingAddress()
    {
        return $this->belongsTo(UsersPlace::class, 'shipping_address_id', 'id');
    }

    // get shipping address
    public function getShippingAddress()
    {
        return $this->shippingAddress()->first();
    }

    public function billingAddress()
    {
        return $this->belongsTo(UsersPlace::class, 'billing_address_id', 'id');
    }

    // get billing address
    public function getBillingAddress()
    {
        return $this->billingAddress()->first();
    }

    public function shippingDues()
    {
        return $this->hasOne(ShippingDue::class, 'order_id', 'id');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    // get updater of order if exists
    public function getUpdater()
    {
        return $this->updater;
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    public function addItem(OrderItem $item)
    {
        $this->items()->save($item);
    }

    public function addItems($items)
    {
        $this->items()->saveMany($items);
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

    /**
     * @return boolean
     */
    public function isOpen()
    {
        return $this->state === self::STATUS_OPEN;
    }

    /**
     * @return boolean
     */
    public function open()
    {
        $this->state = self::STATUS_OPEN;
        return $this->save();
    }

    /**
     * @return boolean
     */
    public function isWantMoreInfo()
    {
        return $this->state === self::STATUS_REQUEST_MORE_INFO;
    }

    /**
     * @return boolean
     */
    public function wantMoreInfo()
    {
        $this->state = self::STATUS_REQUEST_MORE_INFO;
        return $this->save();
    }

    /**
     * @return boolean
     */
    public function isCancelled()
    {
        return $this->state === self::STATUS_CANCELLED;
    }

    /**
     * @return boolean
     */
    public function cancel()
    {
        $this->state = self::STATUS_CANCELLED;
        return $this->save();
    }

    /**
     * @return boolean
     */
    public function isStatusApproved()
    {
        return $this->state === self::STATUS_APPROVED;
    }

    /**
     * @return boolean
     */
    public function isApproved()
    {
        return $this->state === self::STATUS_APPROVED       ||
               $this->state === self::STATUS_UNDER_SHIPPING ||
               $this->state === self::STATUS_DELIVERED;
    }

    /**
     * @return void
     */
    public function approve()
    {
        $this->state = self::STATUS_APPROVED;
        return $this->save();
    }

    /**
     * @return boolean
     */
    public function isRejected()
    {
        return $this->state === self::STATUS_REJECTED;
    }

    /**
     * @return void
     */
    public function reject()
    {
        $this->state = self::STATUS_REJECTED;
        return $this->save();
    }

    /**
     * @return boolean
     */
    public function isUnderShipping()
    {
        return $this->state === self::STATUS_UNDER_SHIPPING;
    }

    /**
     * @return void
     */
    public function underShipping()
    {
        $this->state = self::STATUS_UNDER_SHIPPING;
        return $this->save();
    }

    /**
     * @return boolean
     */
    public function isDelivered()
    {
        return $this->state === self::STATUS_DELIVERED;
    }

    /**
     * @return void
     */
    public function delivered()
    {
        $this->state = self::STATUS_DELIVERED;
        return $this->save();
    }

    public function requireShipping()
    {
        return (bool) $this->require_shipping;
    }

    /**
     * Determine if order state changed or not.
     * @return boolean
     */
    public function stateChanged()
    {
        return $this->find($this->id)->state !== $this->state;
    }

    public function createdBySeller()
    {
        return $this->buyer_id !== $this->created_by;
    }
}