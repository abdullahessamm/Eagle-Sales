<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class InventoryCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'uri_prefix',
        'category_name',
        'description',
        'description_ar',
        'parent_category_id',
        'created_by',
        'updated_by'
    ];

    protected $hidden = [
        'created_by',
        'updated_by'
    ];

    public function showHiddens()
    {
        $this->makeVisible($this->hidden);
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'category_id', 'id');
    }

    public function getItems()
    {
        return $this->items()->get()->all();
    }

    public function withItems()
    {
        $items = $this->getItems();

        // with full Data for each item
        foreach ($items as $item) {
            $item->withFullData();
        }

        $this->items = $item;
        return $this;
    }

    public function getCreatedBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id')->first();
    }

    public function getUpdatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id')->first();
    }

    // get children categories for this category
    public function getChildren()
    {
        return $this->hasMany(InventoryCategory::class, 'parent_category_id', 'id')->get()->all();
    }

    public function withChildren()
    {
        $this->children = $this->getChildren();
        return $this;
    }

    // get available brands in english and arabic from items
    public function getBrands()
    {
        $brands = [];
        $items = $this->items()->get([
            'brand',
            'ar_brand'
        ]);
        foreach ($items as $item) {
            $brand = [
                'en' => $item->brand,
                'ar' => $item->ar_brand
            ];

            if (! in_array($brand, $brands))
                $brands[] = $brand;
        }

        return $brands;
    }

    public function withBrands()
    {
        $this->brands = $this->getBrands();
        return $this;
    }

    public function withFullData()
    {
        $this->withChildren();
        $this->withBrands();
        return $this;
    }

    public function salesAmount(Carbon|null $startDate = null, Carbon|null $endDate = null)
    {
        $startDate = $startDate ?? Carbon::create(1990);
        $endDate   = $endDate ?? now();
        
        $amount = 0;
        $items = $this->items()->get('id');
        $items->each(function ($item) use (&$amount, $startDate, $endDate) {
            $amount += $item->salesAmount($startDate, $endDate);
        });

        return $amount;
    }
}
