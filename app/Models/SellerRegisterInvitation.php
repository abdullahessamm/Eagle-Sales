<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerRegisterInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_seller_id',
        'invitation_owner_id'
    ];
}
