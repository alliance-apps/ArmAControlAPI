<?php

namespace App\Http\Middleware;

use Closure;

class KeyCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->key != config('auth.apikey'))
        {
            die('Altis Life RESTful API v1.0<br>Developed by Tim Vogler ("cat24max")<br>');
        }
        else
        {
            return $next($request);
        }
    }
}
