<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

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
}
