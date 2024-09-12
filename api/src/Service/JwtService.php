<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Service de gestion des JWT
 */
class JwtService
{
    public function __construct(
        #[Autowire('%env(JWT_SECRET_KEY)%')] private $secretKey
    )
    {
    }

    /**
     * Permet de créer les token
     */
    public function createToken($payload)
    {
        $base64UrlHeader = $this->base64UrlEncode(json_encode(["alg" => "HS256", "typ" => "JWT"]));
        $base64UrlPayload = $this->base64UrlEncode(json_encode($payload));
        $base64UrlSignature = hash_hmac('sha256', $base64UrlHeader . '.' . $base64UrlPayload, $this->secretKey, true);
        $base64UrlSignature = $this->base64UrlEncode($base64UrlSignature);
        return $base64UrlHeader . '.' . $base64UrlPayload . '.' . $base64UrlSignature;
    }

    /**
     * Permet d'encoder les données d'un token
     */
    private function base64UrlEncode($data)
    {
        $base64 = base64_encode($data);
        $base64Url = strtr($base64, '+/', '-_');
        return rtrim($base64Url, '=');
    }

    /**
     * Permet de décoder les données d'un token
     */
    private function base64UrlDecode($data)
    {
        $base64 = strtr($data, '-_', '+/');
        $base64Padded = str_pad($base64, strlen($base64) % 4, '=', STR_PAD_RIGHT);
        return base64_decode($base64Padded);
    }

    /**
     * Permet de valider un token
     */
    public function validateToken($token)
    {
        // Implementation for validating JWT
        list($base64UrlHeader, $base64UrlPayload, $base64UrlSignature) = explode('.', $token);

        $signature = $this->base64UrlDecode($base64UrlSignature);
        $expectedSignature = hash_hmac('sha256', $base64UrlHeader . '.' . $base64UrlPayload, $this->secretKey, true);

        return hash_equals($signature, $expectedSignature);
    }

    /**
     * Permet de décodé un token
     */
    public function decodeToken($token)
    {
        // Implementation for decoding JWT
        list(, $base64UrlPayload,) = explode('.', $token);
        $payload = $this->base64UrlDecode($base64UrlPayload);
        return json_decode($payload, true);
    }
}