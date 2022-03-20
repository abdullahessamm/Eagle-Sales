<?php

namespace App\Http\Middleware;

use App\Models\BlockedIp;
use Closure;
use Illuminate\Http\Request;

class RejectRequestsFromBlockedIps
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
        $requestIP = $request->ip();
                
        if (cache()->has('blocked_ips')) {
            $blocked_ips = cache()->get('blocked_ips');
            
            if (in_array($requestIP, $blocked_ips))
                throw new \App\Exceptions\ForbiddenException;
        }

        if (BlockedIp::where('ip', $requestIP)->first())
            throw new \App\Exceptions\ForbiddenException;
        
        return $next($request);
    }
}
