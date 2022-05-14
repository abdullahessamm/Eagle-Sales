<?php

namespace App\Exceptions;

use Exception;

class GoogleMapsException extends Exception
{
    private string $msg;

    public function __construct(string $message)
    {
        $this->msg = $message;
    }

    public function report()
    {
        \App\Models\Errors\InternalError::create([
            'platform' => 0,
            'device_name' => 'server',
            'type' => 'GoogleMaps',
            'code' => 'unknown',
            'message' => $this->msg
        ]);
    }

    public function render()
    {
        return response()->json([
            'success' => false,
            'message' => env('APP_DEBUG') ? $this->msg : 'Server error'
        ], 500);
    }
}
