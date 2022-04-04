<?php

namespace App\Sms;

interface SmsAble
{
    public function sendSms(string $message);
}