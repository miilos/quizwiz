<?php

namespace App\Service\Auth;

use App\Entity\Token;
use App\Entity\User;
use App\Repository\TokenRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class TokenService
{
    public function __construct(
        private readonly TokenRepository $tokenRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    public function generateToken(User $user): Token
    {
        $token = new Token();
        $token->setToken(bin2hex(random_bytes(32)));
        $token->setUser($user);
        $token->setExpiresAt(new DateTimeImmutable('+7 days'));

        $this->entityManager->persist($token);
        $this->entityManager->flush();

        return $token;
    }

    public function getToken(string $token): ?Token
    {
        return $this->tokenRepository->findOneBy(['token' => $token]);
    }

    public function validateToken(Token $token): bool
    {
        return $token->getExpiresAt() > new DateTimeImmutable();
    }
}
