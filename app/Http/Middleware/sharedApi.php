<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Crypt;
use League\Flysystem\Config;

class sharedApi
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
        if (!env('SHAREDAPI', false))
        {
            return $next($request);
        }


        $payload = $request->db;
        $db = Crypt::decrypt($payload);
        $db = json_decode($db);
        config(['database.connections.mysql.host' => $db->host]);
        config(['database.connections.mysql.database' => $db->database]);
        config(['database.connections.mysql.username' => $db->username]);
        config(['database.connections.mysql.password' => $db->password]);
        config(['database.connections.mysql.port' => $db->port]);

        return $next($request);
    }
}
