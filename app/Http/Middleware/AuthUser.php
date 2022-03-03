<?php

namespace App\Http\Middleware;

use App\Events\CacheExpired;
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
        $token = $request->bearerToken();
        
        if (is_null($token))
            return $this->unauthRsponse;

        if (! cache()->has($token)) {
            $this->tokenRecord = auth()->user();
            if (! $this->tokenRecord)
                return $this->unauthRsponse;
            
            event(new CacheExpired('token', 'regenerate', [
                'key' => $token,
                'val' => $this->tokenRecord
            ]));
        } else $this->tokenRecord = cache()->get($token);
        
        if (
            ! $this->checkSerial() ||
            ! $this->checkMAC()
        )
        {
            return response()->json(['enterd']);
            PersonalAccessToken::where('id', $this->tokenRecord['id'])->delete();
            cache()->forget($token);
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
