<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'age',
        'education',
        'l1_address',
        "l1_address_ar",
        "l2_address",
        "l2_address_ar",
        "location_coords",
        "salary",
        "tax",
        "bank_account_number",
        "bank_name"
    ];
}
