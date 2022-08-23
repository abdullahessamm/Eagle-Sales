<?php

namespace App\Http\Middleware;

use App\Models\PersonalAccessToken;
use Closure;
use Illuminate\Http\Request;

class AuthUser
{
    private $unauthRsponse;
    private $tokenRecord;

    public function __construct()
    {
        $this->unauthRsponse = response()->json(['success'=>false], 401);
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $token = auth()->user();
        
        if (! $token)
            return $this->unauthRsponse;

        $this->tokenRecord = $token;
        
        if (
            ! $this->checkSerial() ||
            ! $this->checkMAC()
        )
        {
            PersonalAccessToken::where('id', $this->tokenRecord['id'])->first()->delete();
            return $this->unauthRsponse;
        }
        
        return $next($request);
    }

    private function checkSerial(): bool
    {
        if (request()->has('serial'))
            return request()->get('serial') === $this->tokenRecord['serial'];
        
        return false;
    }

    private function checkMAC(): bool
    {
        if ($this->tokenRecord['device_mac'] === null)
            return true;
        if (request()->has('device_mac'))
            return request()->get('device_mac') === $this->tokenRecord['device_mac'];
        return false;
    }
}
