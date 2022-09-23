<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DuesOfSeller extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'cash',
        'is_reward',
        'reward_type',
        'is_salary',
        'is_commission',
        'tax',
        'order_id',
        'was_withdrawn',
        'notes'
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id', 'id');
    }

    public static function createCommission(int $sellerId, float $cash, int $order_id, $tax = null)
    {
        self::create([
            'seller_id' => $sellerId,
            'cash' => $cash,
            'is_commission' => true,
            'order_id' => $order_id,
            'tax' => $tax,
        ]);
    }
}
