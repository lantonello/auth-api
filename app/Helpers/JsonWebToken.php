<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;

class JsonWebToken
{
    const JWT_EXPIRATION_TIME = 2 * 60 * 60;

    /**
     * Generates and returns de JWT
     * @return string
     */
    public static function generate(User $user)
    {
        // Make the payload
        $payload = [
            'iss' => env('APP_URL'),
            'aud' => env('APP_URL'),
            'sub' => $user->id,
            'nam' => $user->fullname,
            'iat' => time(),
            'exp' => time() + self::JWT_EXPIRATION_TIME
        ];

        return JWT::encode($payload, env('JWT_SECRET'), 'HS256');
    }
}