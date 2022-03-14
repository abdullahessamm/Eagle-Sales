<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "shop_name",
        "l1_address",
        "l1_address_ar",
        "l2_address",
        "l2_address_ar",
        "location_coords",
        "credit_limit",
        "vat_no",
        "category_id",
        "shop_space"
    ];
}
