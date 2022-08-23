<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'uri',
        'pos',
        'is_cover'
    ];

    protected $casts = [
        'pos' => 'array'
    ];

    public $timestamps = false;

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function getItemAttribute()
    {
        return $this->item()->first();
    }

    public function setPosAttribute($value)
    {
        $this->attributes['pos'] = json_encode($value);
    }

}
