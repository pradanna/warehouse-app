<?php

namespace App\Commons\JWT;

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTAuth
{
    public static function encode(JWTClaims $claims)
    {
        $secretKey = config('jwt.secret');
        $issuer = config('jwt.issuer');
        $expiration = config('jwt.exp');
        $issuedAt = time();
        $expirationTime = $issuedAt + $expiration; // jwt valid for 1 hour from the issued time
        $payload = [
            'iat' => $issuedAt,
            'iss' => $issuer,
            'exp' => $expirationTime,
            'data' => [
                'username' => $claims->getUsername(),
                'role' => $claims->getRole(),
            ],
        ];
        return JWT::encode($payload, $secretKey, 'HS256');
    }

    public static function decode($jwt)
    {
        $secretKey = config('jwt.secret');
        try {
            $decoded = JWT::decode($jwt, new Key($secretKey, 'HS256'));
            return [
                'success' => true,
                'message' => 'success decode token',
                'data' => (array)$decoded->claims
            ];
        } catch (ExpiredException $e) {
            return [
                'success' => false,
                'message' => 'token expired',
                'data' => null
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'invalid token',
                'data' => null
            ];
        }
    }
}
