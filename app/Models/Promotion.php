<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'ar_description',
        'start_date',
        'expire_date',
        'item_id',
        'is_discount',
        'has_promocodes',
        'conditional_quantity',
        'number_of_free_elements',
        'amount_of_discount',
        'is_active'
    ];

    protected $dates = [
        'start_date',
        'expire_date',
    ];

    public function getItem()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id')->first();
    }

    // activate the promotion
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

    // get the promotion's item
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id')->first();
    }

    // update description
    public function updateDescription($description, $ar_description)
    {
        $this->description = $description ?? $this->description;
        $this->ar_description = $ar_description ?? $this->ar_description;
        try {
            $this->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }

        return $this;
    }
}
