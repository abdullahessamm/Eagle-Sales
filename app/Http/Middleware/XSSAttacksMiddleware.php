<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class XSSAttacksMiddleware
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
        $pattern = '/<.+>.*< *\/.+>/';
        $data = $request->all();
        foreach ($data as $key => $val) {
            if (preg_match($pattern, $val))
                throw new \App\Exceptions\XSSAttackAttempt();
        }
        return $next($request);
    }
}
