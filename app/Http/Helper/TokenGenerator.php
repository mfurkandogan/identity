<?php


namespace App\Http\Helper;


use App\Models\User;
use Firebase\JWT\JWT;

class TokenGenerator
{
    public function generateToken(User $user): string
    {
        $time = time() + env('JWT_TTL');
        $payload = [
            'iss' => 'lumen-jwt',
            'sub' => $user->id,
            'iat' => time(),
            'exp' => $time,
            'user' => $user
        ];
        return JWT::encode($payload, env('JWT_SECRET'));
    }
}
