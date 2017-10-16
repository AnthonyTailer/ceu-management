<?php
/**
 * Created by PhpStorm.
 * User: tailer
 * Date: 26/09/17
 * Time: 16:52
 */

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;

class AuthJWT
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
        try {
            $user = JWTAuth::toUser($request->input('token'));
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json(['error'=>'Token is Invalid'], 403);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return response()->json(['error'=>'Token is Expired'], 401);
            }else{
                return response()->json(['error'=>'Something is wrong'], 500);
            }
        }
        return $next($request);
    }
}