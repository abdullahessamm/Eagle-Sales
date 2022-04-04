<?php

namespace App\Models;

use App\Models\Traits\UsersTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'location_coords',
        'l1_address',
        'l1_address_ar',
        'l2_address',
        'l2_address_ar'
    ];

    protected $hidden = [
        'vat_no',
        'fb_page',
        'website_domain',
        'location_coords',
        'l1_address',
        'l1_address_ar',
        'l2_address',
        'l2_address_ar',
    ];

    public function relatedOrders()
    {
        return $this->hasMany(Order::class, 'supplier_id', 'id');
    }

    public function relatedItems()
    {
        return $this->hasMany(Item::class, 'supplier_id', 'id');
    }

    public function getRelatedItems()
    {
        return $this->relatedItems()->get()->all();
    }
}
