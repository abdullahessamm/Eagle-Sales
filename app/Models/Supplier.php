<?php

namespace App\Models;

use App\Models\Traits\UsersTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Supplier extends Model
{
    use HasFactory, UsersTrait;

    protected $fillable = [
        'user_id',
        'vat_no',
        'shop_name',
        'whatsapp_no',
        'fb_page',
        'website_domain',
    ];

    protected $hidden = [
        'vat_no',
        'fb_page',
        'website_domain',
    ];

    public function relatedOrders()
    {
        return $this->hasMany(Order::class, 'supplier_id', 'id');
    }

    public function dues()
    {
        return $this->hasMany(SupplierDue::class, 'supplier_id', 'id');
    }

    public function commissions()
    {
        return $this->hasMany(OurCommission::class, 'supplier_id', 'id');
    }

    public function relatedItems()
    {
        return $this->hasMany(Item::class, 'supplier_id', 'id');
    }

    public function relatedShippingDues()
    {
        return $this->hasMany(ShippingDue::class, 'supplier_id', 'id');
    }

    public function getShippingDuesAttribute()
    {
        return $this->relatedShippingDues()->get();
    }

    public function getOrdersAttribute()
    {
        return $this->relatedOrders()->get();
    }

    public function getItemsAttribute()
    {
        return $this->relatedItems()->get();
    }

    public function addItem(Item $item)
    {
        return $this->relatedItems()->save($item);
    }

    public function ordersAmount(int $state, Carbon $startDate, Carbon $endDate)
    {
        return $this->relatedOrders()
        ->whereBetween('created_at', [$startDate, $endDate])
        ->where('state', $state)
        ->get()
        ->sum('total_required');
    }

    public function salesAmount()
    {
        return $this->relatedOrders()
        ->where('state', Order::STATUS_DELIVERED)
        ->get()
        ->sum('total_required');
    }
}
