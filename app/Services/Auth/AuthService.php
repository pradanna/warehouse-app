<?php

namespace App\Services\Auth;

use App\Commons\Http\ServiceResponse;
use App\Commons\JWT\JWTAuth;
use App\Commons\JWT\JWTClaims;
use App\Models\User;
use App\Schemas\Auth\LoginSchema;

use App\Schemas\Auth\RefreshTokenSchema;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;

class AuthService implements AuthServiceInterface
{
    public function login(LoginSchema $schema): ServiceResponse
    {
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray());
            }
            $schema->hydrateBody();
            $user = User::with(['roles'])
                ->where('username', '=', $schema->getUsername())
                ->first();
            if (!$user) {
                return ServiceResponse::notFound('user not found');
            }
            $isPasswordValid = Hash::check($schema->getPassword(), $user->password);
            if (!$isPasswordValid) {
                return ServiceResponse::unauthorized('password did not match');
            }

            $roles = $user->roles->pluck('name')->toArray();
            $jwtClaims = new JWTClaims($user->username, $roles);
            $token = JWTAuth::encode($jwtClaims);
            $refreshToken = JWTAuth::encodeRefreshToken($user->username);
            $payload = [
                'access_token' => $token,
                'refresh_token' => $refreshToken
            ];
            return ServiceResponse::statusOK("successfully login", $payload);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }


    public function refreshToken(RefreshTokenSchema $schema): ServiceResponse
    {
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray());
            }
            $schema->hydrateBody();

            $decodeResponse = JWTAuth::decodeRefreshToken($schema->getRefreshToken());
            if (!$decodeResponse['success']) {
                return ServiceResponse::unauthorized($decodeResponse['message']);
            }

            $user = User::with(['roles'])
                ->where('username', '=', $decodeResponse['data'])
                ->first();
            if (!$user) {
                return ServiceResponse::notFound('user not found');
            }
            $roles = $user->roles->pluck('name')->toArray();
            $jwtClaims = new JWTClaims($user->username, $roles);
            $token = JWTAuth::encode($jwtClaims);
            $payload = [
                'access_token' => $token,
            ];
            return ServiceResponse::statusOK("successfully refresh token", $payload);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }
}
