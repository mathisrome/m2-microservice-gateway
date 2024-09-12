<?php

namespace App\Controller\Api;

use App\Service\JwtService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function index(
        Request $request,
        JwtService $jwtService,
        HttpClientInterface $httpClient
    ): JsonResponse
    {
        $response = $httpClient->request(
            "POST",
            "http://customer-symfony-nginx/api/login",
            [
                "headers" => [
                    "Content-Type" => "application/json",
                ],
                "body" => $request->getContent()
            ]
        );

        $token = json_decode($response->getContent(), true);

        return $this->json($token);
    }
}
