<?php

namespace App\Models;

use App\Models\Traits\UsersTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Seller extends Model
{
    use HasFactory, UsersTrait;

    protected $fillable = [
        'user_id',
        'age',
        'education',
        "salary",
        "tax",
        "bank_account_number",
        "bank_name"
    ];

    protected $hidden = [
        'education',
        "salary",
        "tax",
        "bank_account_number",
        "bank_name",
    ];

    public function dues()
    {
        return $this->hasMany(DuesOfSeller::class, 'seller_id', 'id');
    }

    public function relatedOrders()
    {
        return $this->getUser()->orders();
    }

    public function ordersAmount(int|null $state = null, Carbon|null $startDate = null, Carbon|null $endDate = null)
    {
        if (! $startDate)
            $startDate = Carbon::create(1990);

        if (! $endDate)
            $endDate = Carbon::now();

        $rel = $this->relatedOrders();
        if ($state)
            $rel->where('state', $state);
        
        return $rel
        ->whereBetween('created_at', [$startDate, $endDate])
        ->get()
        ->sum('total_required');
    }

    public function journeyPlans()
    {
        return $this->hasMany(JourneyPlan::class, 'seller_id', 'id');
    }

    public function salesAmount()
    {
        return $this->ordersAmount(Order::STATUS_DELIVERED);
    }
}
