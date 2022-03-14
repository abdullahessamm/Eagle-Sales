<?php

namespace App\Http\Middleware\Permissions\Sellers;

use Closure;
use Illuminate\Http\Request;

class CreateSellerMiddleware
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
        $userData = auth()->user()->userData;
        $permissions = $userData->userInfo->permissions;
        $permissionsOnSellers = $permissions->sellers_access_level;
        $creatable = substr($permissionsOnSellers, 0, 1);

        if ($creatable)
            return $next($request);

        throw new \App\Exceptions\ForbiddenException();
    }
}
