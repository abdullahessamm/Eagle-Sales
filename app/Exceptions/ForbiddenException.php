<?php

namespace App\Exceptions;

use Exception;

class ForbiddenException extends Exception
{

    public function __construct($message = null, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render()
    {
        return response()->json(['success' => false, 'message' => $this->message], 403);
    }
}
