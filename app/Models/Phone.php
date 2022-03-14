<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'user_id',
        'verify_code',
        'verify_code_expires_at',
        'verified_at'
    ];

    public function genVerifyCode(): void
    {
        $this->verify_code              = rand(100000,999999);
        $this->verify_code_expires_at   = now()->addMinutes(15);
    }
}
