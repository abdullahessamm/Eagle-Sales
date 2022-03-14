<?php

namespace App\Exceptions;

use Exception;

class MailException extends Exception
{
    private $e;

    public function __construct($e)
    {
        $this->e = $e;
    }

    public function report()
    {
        \App\Models\Errors\InternalError::create([
            'platform' => 0,
            'device_name' => 'server',
            'type' => get_class($this->e),
            'code' => $this->e->getCode(),
            'message' => $this->e->message
        ]);
    }

    public function render()
    {
        return response()->json(['success' => false], 500);
    }
}
