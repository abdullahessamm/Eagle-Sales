<?php

namespace App\Sms;

class SendSms implements SmsAble
{
    use SmsTrait;

    public string $phone;

    public function __construct(string $phone)
    {
        $this->phone = $phone;
    }
}