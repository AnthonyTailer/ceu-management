<?php

namespace App\Http\Middleware;

use Closure;

class Cors
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
        $domains = ['http://localhost:8081'];

        if (isset($request->server()['HTTP_ORIGIN'])) {
            $origin = $request->server()['HTTP_ORIGIN'];
            if (in_array($origin, $domains)) {
                header('Access-Control-Allow-Origin: *');
                header('Access-Control-Allow-Headers: Origin, Content-Type, Authorization');
                header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');
                header('Access-Control-Expose-Headers: Origin, Content-Type, Authorization');
            }
        }

        return $next($request);
    }
}
