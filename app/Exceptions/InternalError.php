<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\QueryException;

class InternalError extends Exception
{
    private $exception;

    public function __construct($exception)
    {
        $this->exception = $exception;
    }

    public function report()
    {
        \App\Models\Errors\InternalError::create([
            'platform' => 0,
            'device_name' => 'server',
            'type' => get_class($this->exception),
            'code' => $this->exception->getCode(),
            'message' => $this->exception->getMessage()
        ]);
    }

    public function render()
    {
        return response()->json(['success' => false], 500);
    }
}
