<?php

namespace App\Models;

use App\Sms\SmsAble;
use App\Sms\SmsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phone extends Model implements SmsAble
{
    use HasFactory, SmsTrait;

    protected $fillable = [
        'phone',
        'user_id',
        'verify_code',
        'is_primary',
        'verify_code_expires_at',
        'verified_at'
    ];

    protected $dates = [
        'verify_code_expires_at',
        'verified_at',
    ];

    protected $hidden = [
        'verify_code'
    ];

    public function genVerifyCode(): void
    {
        $this->verify_code              = rand(100000,999999);
        $this->verify_code_expires_at   = now()->addDay();
    }

    public function sendVerifyCode(): void
    {
        $message = "Your verify code is: {$this->verify_code}\nplease enter it to verify your phone number";
        $this->sendSms($message);
    }
}
