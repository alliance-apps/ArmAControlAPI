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
            die('ArmA Control Altis Life RESTful API v1.3.2 (2019-03-20)<br>Developed by Tim Vogler ("cat24max")<br>Distributed by AllianceApps.de');
        }
        else
        {
            return $next($request);
        }
    }
}
