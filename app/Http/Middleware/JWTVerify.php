<?php

namespace App\Http\Middleware;

use App\Commons\Http\APIResponse;
use App\Commons\JWT\JWTAuth;
use App\Models\User;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JWTVerify
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return JsonResponse|mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        if (!$token) {
            return APIResponse::toJSONResponse(
                401,
                'token is required'
            );
        }

        $decodeResponse = JWTAuth::decode($token);
        if (!$decodeResponse['success']) {
            return APIResponse::toJSONResponse(
                401,
                $decodeResponse['message']
            );
        }
        $userData = $decodeResponse['data'];
        $user = User::with([])
            ->where('username', '=', $userData['username'])
            ->first();
        if (!$user) {
            return APIResponse::toJSONResponse(
                401,
                'Invalid User Account'
            );
        }
        Auth::setUser($user);
        return $next($request);
    }
}
