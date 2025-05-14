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
            'claims' => [
                'username' => $claims->getUsername(),
                'roles' => $claims->getRole(),
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

    public static function encodeRefreshToken($subject)
    {
        $secretKey = config('jwt.secret_refresh');
        $issuedAt = time();
        $expirationTime = $issuedAt + (30 * 24 * 60 * 60); //expired on 30 days
        $payload = array(
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'sub' => $subject,
        );
        return JWT::encode($payload, $secretKey, 'HS256');
    }

    public static function decodeRefreshToken($jwt)
    {
        $secretKey = config('jwt.secret_refresh');
        try {
            $decoded = JWT::decode($jwt, new Key($secretKey, 'HS256'));
            return [
                'success' => true,
                'message' => 'success decode refresh token',
                'data' => $decoded->sub
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
