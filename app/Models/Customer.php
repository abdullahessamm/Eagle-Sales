<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\Traits\UsersTrait;

class Customer extends Model
{
    use HasFactory, UsersTrait;

    protected $fillable = [
        "user_id",
        "shop_name",
        "credit_limit",
        "vat_no",
        "category_id",
        "shop_space"
    ];

    protected $hidden = [
        "credit_limit",
        "vat_no",
        "shop_space",
    ];

    public function relatedOrders()
    {
        return $this->hasMany(Order::class, 'customer_id', 'id');
    }

    public function getRelatedOrders()
    {
        return $this->relatedOrders()->get()->all();
    }

    public function withRelatedOrders()
    {
        $this->orders = $this->getRelatedOrders();
        return $this;
    }

}
