<?php

namespace App\Exceptions;

use Exception;

class NotSupportedCountry extends Exception
{
    private string $clientCountryIsoCode;

    public function __construct(string $clientCountryIsoCode = '')
    {
        $this->clientCountryIsoCode = $clientCountryIsoCode;
    }

    public function render()
    {
        if (request()->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'This application is not supported in your country',
            ], 406);
        }

        return view('errors.not_supported_country');
    }
}
