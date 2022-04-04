<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
}

/**
 * Order state will be as below:
 * 0 -> pending if customer is waiting for approval
 * 1 -> opened
 * 2 -> request more info
 * 3 -> canceled
 * 4 -> approved
 * 5 -> delivered
 */