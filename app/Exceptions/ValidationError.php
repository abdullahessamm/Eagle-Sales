<?php

namespace App\Exceptions;

use Exception;

class ValidationError extends Exception
{
    private array $errors;
    private bool $debuggingMode;

    public function __construct(array $errors)
    {
        $this->errors = $errors;
        $this->debuggingMode = env('APP_DEBUG');
    }

    public function render()
    {
        if ($this->debuggingMode)
            return response()->json([
                'success' => false,
                'errors' => $this->errors
            ], 400);

        return response()->json(['success' => false], 400);
    }
}
