<?php

namespace App\Services\Auth;

use App\Commons\Http\ServiceResponse;
use App\Commons\JWT\JWTAuth;
use App\Commons\JWT\JWTClaims;
use App\Models\User;
use App\Schemas\Auth\LoginSchema;
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

    public function refresh(Request $request): ServiceResponse
    {
        try {
            $refreshToken = $request->input('refresh_token');
            if (!$refreshToken) {
                return ServiceResponse::unauthorized('refresh token is required');
            }

            // ➜ 1. verifikasi refresh token
            $decoded = JWTAuth::decodeRefreshToken($refreshToken);
            if (!$decoded) {
                return ServiceResponse::unauthorized('refresh token invalid');
            }

            // ➜ 2. ambil username dari payload
            $payloadObj = (object) ($decoded['data'] ?? []);
            $username   = $payloadObj->sub ?? null;
            $user     = User::with('roles')->where('username', $username)->first();

            if (!$user) {
                return ServiceResponse::notFound('user not found');
            }

            // ➜ 3. buat access token (dan refresh token baru jika mau)
            $roles  = $user->roles->pluck('name')->toArray();
            $claims = new JWTClaims($username, $roles);

            $newAccessToken  = JWTAuth::encode($claims);           // exp: 10‑15 menit
            $newRefreshToken = JWTAuth::encodeRefreshToken($username); // exp: 7‑30 hari

            return ServiceResponse::statusOK('token refreshed', [
                'access_token'  => $newAccessToken,
                'refresh_token' => $newRefreshToken,
            ]);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }
}
