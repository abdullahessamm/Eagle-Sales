<?php

namespace App\Http\Middleware;

use App\Models\AvailableCountry;
use Closure;
use Illuminate\Http\Request;

class RejectNotSupportedCountries
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);

        $availableCountries = AvailableCountry::all('iso_code');
        $clientCountry = geoip()->getLocation()->iso_code;

        if (!in_array($clientCountry, $availableCountries->pluck('iso_code')->toArray()))
            throw new \App\Exceptions\NotSupportedCountry($clientCountry);

        return $next($request);
    }
}
