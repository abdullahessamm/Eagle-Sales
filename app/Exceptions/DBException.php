<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\QueryException;

class DBException extends Exception
{
    private QueryException $e;

    public function __construct(QueryException $e)
    {
        $this->e = $e;
    }

    public function report()
    {
        \App\Models\Errors\InternalError::create([
            'platform' => 0,
            'device_name' => 'server',
            'type' => QueryException::class,
            'code' => $this->e->getCode(),
            'message' => $this->e->message
        ]);
    }

    public function render()
    {
        return response()->json(['success' => false], 500);
    }
}
