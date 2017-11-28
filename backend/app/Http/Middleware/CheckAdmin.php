<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;

class CheckAdmin
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
        $user = JWTAuth::toUser($request->token);
        if ($user->is_admin == 0) {
            return response()->json(['error'=>'Is not ADMIN']);
        }

        return $next($request);
    }
}
