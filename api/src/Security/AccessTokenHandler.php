<?php

namespace App\Security;

use App\Repository\AccessTokenRepository;
use App\Service\JwtService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

/**
 * Gestion des token lors de la connexion
 */
class AccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private JwtService   $jwtService,
    )
    {
    }

    /**
     * {@inheritDoc}
     */
    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        if (empty($accessToken)) {
            throw new BadCredentialsException('Invalid credentials.');
        }

        $payload = $this->jwtService->decodeToken($accessToken);


        $iat = new \DateTimeImmutable($payload["iat"]["date"]);
        $exp = $iat->modify("+1 hour");

        $now = new \DateTimeImmutable();

        if (false === ($this->jwtService->validateToken($accessToken) && $exp > $now)) {
            throw new BadCredentialsException('Invalid credentials.');
        }

        // and return a UserBadge object containing the user identifier from the found token
        // (this is the same identifier used in Security configuration; it can be an email,
        // a UUID, a username, a database ID, etc.)
        return new UserBadge('api');
    }
}