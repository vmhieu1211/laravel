<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class FakeLoginController
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $params = null)
    {
        if($params !== 'admin'){
            return redicrect()->route("not.permission");
        }
        return $next($request);
    }
}