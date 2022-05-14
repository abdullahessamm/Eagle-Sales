<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class XSSAttacksMiddleware
{
    private int $max_deep = 5;

    private function throwAttackExceptionWhenXSSIsDetected($request)
    {
        $xssPattern = '/<.+>.*< *\/.+>/';
        $requestParams = $request->all();
        $requestParams = $this->getAllNestedParams($requestParams);

        foreach ($requestParams as $param) {
            if (preg_match($xssPattern, $param)) {
                throw new \App\Exceptions\XSSAttackAttempt();
            }
        }

    }

    // get all the nested parameters with it's deep level of nesting if deep level is more than 5 then return
    private function getAllNestedParams($params, $deep = 0)
    {
        $nestedParams = [];
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                if ($deep < $this->max_deep) {
                    $nestedParams = array_merge($nestedParams, $this->getAllNestedParams($value, $deep + 1));
                } else {
                    throw new \App\Exceptions\ValidationError([
                        'message' => 'Deep level of nesting is more than 5'
                    ]);
                }
            } else {
                $nestedParams[] = $value;
            }
        }
        return $nestedParams;
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
        $this->throwAttackExceptionWhenXSSIsDetected($request);
        return $next($request);
    }
}
