<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JourneyPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'order_id',
        'date',
        'location_coords',
        'address',
        'ar_address',
        'has_been_visited',
        'visited_at',
        'created_by',
        'updated_by',
    ];

    protected $dates = [
        'date',
        'visited_at',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
