<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;

class Uom extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ar_name',
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

    /**
     * change the default uom of the item to the new uom without commiting the changes
     *
     * @return Uom current uom object
     */
    public function setDefault()
    {

        if ($this->is_default)
            return $this;

        // delete default uom
        $this->where('item_id', $this->item_id)->where('is_default', 1)->delete();

        // delete conversion rule
        $this->deleteConversionRule();
        
        // set this uom to default
        $this->is_default = true;
        return $this;
    }

    /**
     * Create or update a uom's conversion rule
     *
     * @param float $factor
     * @param boolean $isMultiply
     * @return boolean true if success, false if uom is default
     * @throws \App\Exceptions\DBException if the query fails
     */
    public function setConversionRule(float $factor, bool $isMultiply)
    {
        if ($this->is_default)
            return false;

        try {
            UomConversionRule::updateOrCreate(
                ['uom_id' => $this->id],
                ['factor' => $factor, 'operation_is_multiply' => $isMultiply]
            );
        } catch (QueryException $e) {
            $this->delete();
            throw new \App\Exceptions\DBException($e);
        }

        return true;
    }

    public function conversionRule() {
        return $this->hasOne(UomConversionRule::class, 'uom_id', 'id');
    }

    public function getConversionRule()
    {
        if ($this->is_default)
            return;
        return $this->hasOne(UomConversionRule::class, 'uom_id', 'id')->first();
    }

    public function deleteConversionRule()
    {
        if ($this->is_default)
            return;
        $this->hasOne(UomConversionRule::class, 'uom_id', 'id')->delete();
    }

    public function calculateUnitPrice(Item $item=null)
    {
        $item = $item ?? $this->getItem();
        $itemPrice = (float) $item->price;
        if ($this->is_default)
            return $itemPrice;

        $conversionRule = $this->conversionRule()->first();
        $factor = (float) $conversionRule->factor;
        $isMultiply = (bool) $conversionRule->operation_is_multiply;
        
        $unitPrice = $isMultiply ? $itemPrice * $factor : $itemPrice / $factor;
        return round($unitPrice, 2);
    }

    public function withPrice()
    {
        $this->price = $this->calculateUnitPrice();
        return $this;
    }
}
