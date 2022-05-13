<?php

namespace App\Sms;

use App\Models\Errors\InternalError;

trait SmsTrait
{
    public function sendSms(string $message)
    {
        if (! env('ENABLE_TWILIO'))
            return;

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
            InternalError::create([
                'platform' => 0,
                'device_name' => 'server',
                'type' => get_class($e),
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]);
        }
    }
}