<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLanguage
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
        $supportedLanguages = ['en', 'ar'];
        // if request has lang cookie, set locale else set default locale to en
        if ($request->hasCookie('lang')) {
            $lang = $request->cookie('lang');
            
            if (in_array($lang, $supportedLanguages))
                app()->setLocale($lang);
            else
                app()->setLocale('en');

        } else {
            app()->setLocale('en');
        }
        return $next($request);
    }
}
