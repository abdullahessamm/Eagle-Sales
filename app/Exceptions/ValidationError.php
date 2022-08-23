<?php

namespace App\Exceptions;

use Exception;

class ValidationError extends Exception
{
    private array $errors;
    private bool $debuggingMode;

    public function __construct(array $errors, ?bool $showErrors = null)
    {
        $this->errors = $errors;
        $this->debuggingMode = $showErrors ?? env('APP_DEBUG');
    }

    public function getErrors() {
        return $this->errors;
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
