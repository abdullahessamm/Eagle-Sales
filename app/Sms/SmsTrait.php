<?php

namespace App\Sms;

trait SmsTrait
{
    public function sendSms(string $message)
    {
        $sid = env('TWILIO_SID') ?? false;
        $token = env('TWILIO_TOKEN') ?? false;
        $twilioPhone = env('TWILIO_PHONE') ?? false;

        if (! $sid || ! $token || ! $twilioPhone)
            return;

        $client = new \Twilio\Rest\Client($sid, $token);
        
        try {
            $client->messages->create($this->phone, [
                'from' => $twilioPhone,
                'body' => $message,
            ]);
        } catch (\Twilio\Exceptions\RestException $e) {
            throw new \App\Exceptions\InternalError($e);
        }
    }
}