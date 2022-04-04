<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UomConversionRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'uom_id',
        'factor',
        'operation_is_multiply',
    ];
}
