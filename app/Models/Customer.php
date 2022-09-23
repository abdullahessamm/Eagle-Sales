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
        "vat_uri",
        "credit_limit",
        "category_id",
        "shop_space",
    ];

    protected $hidden = [
        "vat_uri",
        "credit_limit",
        "shop_space",
    ];
}
