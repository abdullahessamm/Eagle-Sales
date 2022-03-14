<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class AttackAttempt extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'attack_type',
        'ip',
        'user_id',
        'attempt_uri',
        'attempt_time'
    ];

    public function setAttemptTime(Carbon $time = null)
    {
        $this->attempt_time = $time ?? now();
    }
}
