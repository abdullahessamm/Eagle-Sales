<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;

class Item extends Model
{
    use HasFactory;

    const AVAILABLE_COLUMNS = [
        'name',
        'ar_name',
        'category_id',
        'brand',
        'ar_brand',
        'barcode',
        'keywards',
        'describtion',
        'ar_describtion',
        'has_promotions',
        'is_var',
        'is_available',
        'video_uri',
        'total_available_count',
        'is_approved',
        'supplier_id',
        'is_active',
        'price',
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'name',
        'ar_name',
        'category_id',
        'brand',
        'ar_brand',
        'barcode',
        'keywards',
        'describtion',
        'ar_describtion',
        'has_promotions',
        'is_var',
        'is_available',
        'video_uri',
        'total_available_count',
        'is_approved',
        'supplier_id',
        'is_active',
        'price'
    ];

    public function activate(bool $is_activate)
    {
        $this->is_active = $is_activate;
        try {
            $this->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }

        return $this;
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id')->first();
    }

    public function getCategory()
    {
        return $this->belongsTo(InventoryCategory::class, 'category_id', 'id')->first();
    }

    public function withCategory()
    {
        $this->category = $this->getCategory();
        return $this;
    }

    public function getImages()
    {
        return $this->hasMany(ItemImage::class, 'item_id', 'id')->get()->all();
    }

    public function withImages()
    {
        $this->images = $this->getImages();
        return $this;
    }

    public function addComment(ItemsComment $comment)
    {
        $comment->item_id = $this->id;
        try {
            $comment->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }
    }

    public function getComments()
    {
        return $this->hasMany(ItemsComment::class, 'item_id', 'id')->get()->all();
    }

    public function withComments()
    {
        $this->comments = $this->getComments();
        return $this;
    }

    public function addRate(int $rate)
    {
        $rate = new ItemsRate;
        $rate->rate = $rate;
        $rate->item_id = $this->id;
        $rate->customer_id = auth()->user()->userData->userInfo->id;

        try {
            $rate->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }
    }

    public function getRates()
    {
        return $this->hasMany(ItemsRate::class, 'item_id', 'id')->get()->all();
    }

    public function calculateRate()
    {
        $allRates = $this->getRates();
        $countOfRates = count($allRates);
        if ($countOfRates === 0)
            return 0;
        $ratesSum = 0;
        foreach ($allRates as $rate)
            $ratesSum += (int) $rate->rate;

        return $ratesSum/$countOfRates;
    }

    public function withRate()
    {
        $this->rate = $this->calculateRate();
        return $this;
    }

    public function getPromotions()
    {
        return $this->hasMany(Promotion::class, 'item_id', 'id')->get()->all();
    }

    // add promotion to item
    public function addPromotion(Promotion $promotion)
    {
        $this->has_promotions = true;
        try {
            $this->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }

        $promotion->item_id = $this->id;
        try {
            $promotion->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }
    }

    public function withPromotions()
    {
        $this->promotions = $this->getPromotions();
        return $this;
    }

    public function addUOM
    (
        string $name,
        string $arName,
        string $description=null,
        string $arDescription=null,
        bool $isDefault=false
    )
    {
        $defaultUOM = $this->hasMany(Uom::class, 'item_id', 'id')->where('is_default',true)->first();
        
        if ($defaultUOM)
            return false;

        try {
            $uom = new Uom;
            $uom->uom_name = $name;
            $uom->ar_uom_name = $arName;
            $uom->description = $description;
            $uom->ar_description = $arDescription;
            $uom->is_default = $isDefault;
            $uom->item_id = $this->id;
            $uom->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }
    }

    public function getUOMs()
    {
        $UOMs = $this->hasMany(Uom::class, 'item_id', 'id')->get()->all();
        foreach ($UOMs as $UOM)
            $UOM->withPrice();
        return $UOMs;
    }

    // update uom by id
    public function updateUOM
    (
        int $id,
        string $name,
        string $arName,
        string $description=null,
        string $arDescription=null,
        bool $isDefault=false
    )
    {
        $uom = $this->hasMany(Uom::class, 'item_id', 'id')->where('id',$id)->first();
        if (!$uom)
            throw new \App\Exceptions\NotFoundException(get_class($this), $id);

        try {
            $uom->uom_name = $name;
            $uom->ar_uom_name = $arName;
            $uom->description = $description;
            $uom->ar_description = $arDescription;
            $uom->is_default = $isDefault;
            $uom->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }
    }

    // delete uom by id
    public function deleteUOM(int $id)
    {
        $uom = $this->hasMany(Uom::class, 'item_id', 'id')->where('id',$id)->first();
        if (!$uom)
            throw new \App\Exceptions\NotFoundException(get_class($this), $id);

        try {
            $uom->delete();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }
    }

    public function withUOMs()
    {
        $this->UOMs = $this->getUOMs();
        return $this;
    }

    public function getFullObject()
    {
        $fullItemObject = clone $this;
        return $fullItemObject
        ->withCategory()
        ->withUOMs()
        ->withPromotions()
        ->withComments()
        ->withRate()
        ->withComments()
        ->withImages();
    }

    public function withFullData()
    {
        return $this
        ->withCategory()
        ->withUOMs()
        ->withPromotions()
        ->withComments()
        ->withRate()
        ->withComments()
        ->withImages();
    }

    /**
     * 
     *
     * @param Uom $uom
     * @param integer $quantity
     * @return array
     */
    public function sell(Uom $uom, int $quantity, Promotion $promotion=null)
    {
        if ($quantity > $this->total_available_count)
            return false;

        $unitPrice = $uom->calculateUnitPrice($this);
        
        $totalPrice = $unitPrice * $quantity;

        if ($promotion) {
            if ($promotion->start_date->isPast() && $promotion->expire_date->isFuture()):
                $discount = $promotion->is_discount ?
                            floor($quantity/$promotion->conditional_quantity) * $promotion->amount_of_discount
                            : 0;

                $freeElements = $promotion->is_discount ? 0 : floor($quantity/$promotion->conditional_quantity);
            endif;
        }

        $totalPriceAfterDiscount = $totalPrice - $discount;
        return [
            'total' => $totalPrice,
            'free_elements' => $freeElements,
            'discount' => $discount,
            'total_after_discount' => $totalPriceAfterDiscount
        ];
    }

    // approve item
    public function approve()
    {
        try {
            $this->is_approved = true;
            $this->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }
    }

    // reject item
    public function reject()
    {
        try {
            $this->is_approved = false;
            $this->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }
    }
    
}
