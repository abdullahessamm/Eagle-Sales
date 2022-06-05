<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;

class Uom extends Model
{
    use HasFactory;

    protected $fillable = [
        'uom_name',
        'ar_uom_name',
        'description',
        'ar_description',
        'weight',
        'length',
        'width',
        'height',
        'item_id',
        'is_default',
    ];

    public function getItem()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id')->first();
    }

    public function addConversionRule(float $factor, bool $isMultiply)
    {
        if ($this->is_default)
            return false;

        try {
            $conversionRule = new UomConversionRule;
            $conversionRule->factor = $factor;
            $conversionRule->operation_is_multiply = $isMultiply;
            $conversionRule->uom_id = $this->id;
            $conversionRule->save();
        } catch (QueryException $e) {
            $this->delete();
            throw new \App\Exceptions\DBException($e);
        }

        return true;
    }

    public function getConversionRule()
    {
        if ($this->is_default)
            return;
        return $this->hasOne(UomConversionRule::class, 'uom_id', 'id')->first();
    }

    public function calculateUnitPrice(Item $item=null)
    {
        $item = $item ?? $this->getItem();
        $itemPrice = (float) $item->price;
        if ($this->is_default)
            return $itemPrice;

        $conversionRule = $this->getConversionRule();
        $factor = (float) $conversionRule->factor;
        $isMultiply = (bool) $conversionRule->operation_is_multiply;
        
        return $isMultiply ? $itemPrice * $factor : $itemPrice / $factor;
    }

    public function withPrice()
    {
        $this->price = $this->calculateUnitPrice();
        return $this;
    }
}
