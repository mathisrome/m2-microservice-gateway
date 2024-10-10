<?php

namespace App\Security;

use App\Entity\Application;
use App\Repository\AccessTokenRepository;
use App\Service\JwtService;
use Doctrine\ORM\EntityManagerInterface;
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
        private EntityManagerInterface $em
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

        $application = $this->em->getRepository(Application::class)->findOneByApiKey($accessToken);

        if (!empty($application)) {
            return new UserBadge('api');
        }

        $payload = $this->jwtService->decodeToken($accessToken);

        $exp = new \DateTimeImmutable($payload["exp"]["date"]);
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