<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class JwtDecoderService
{
    private string $publicKey;

    public function __construct()
    {
        $this->publicKey = file_get_contents(__DIR__ . '/../../config/jwt/public.pem'); // Завантажуємо публічний ключ
    }

    public function decodeToken(Request $request): array
    {
        $authHeader = $request->headers->get('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            throw new UnauthorizedHttpException('Bearer', 'JWT Token not found');
        }

        $jwt = substr($authHeader, 7); // Видаляємо "Bearer "

        try {
            return (array) JWT::decode($jwt, new Key($this->publicKey, 'RS256'));
        } catch (\Exception $e) {
            throw new UnauthorizedHttpException('Bearer', 'Invalid JWT Token: ' . $e->getMessage());
        }
    }
}
