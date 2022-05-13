<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\QueryException;

class InternalError extends Exception
{
    private array $error;

    public function __construct($exception)
    {
        $this->error = [
            'platform' => 0,
            'device_name' => 'server',
            'type' => get_class($exception),
            'code' => $exception->getCode(),
            'message' => $exception->getMessage()
        ];
    }

    public function report()
    {
        \App\Models\Errors\InternalError::create($this->error);
    }

    public function render()
    {
        $message = $this->error;
        unset($message['platform']);
        unset($message['device_name']);
        
        if (! env('App_DEBUG'))
            $message = 'Server error';

        return response()->json([
            'message' => $message
        ], 500);
    }
}
