<?php

namespace App\Security;

use App\Repository\TokenRepository;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class TokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private readonly TokenRepository $tokenRepository,
    ) {}

    public function getUserBadgeFrom(#[\SensitiveParameter] string $accessToken): UserBadge
    {
        $token = $this->tokenRepository->findOneBy(['token' => $accessToken]);

        if (null === $token || !$token->isValid()) {
            throw new BadCredentialsException('Invalid or expired token!');
        }

        return new UserBadge($token->getUser()->getEmail());
    }
}
