<?php

namespace App\GraphQL\Resolver;

use App\Entity\QuizAttempt;
use App\Repository\QuizAttemptRepository;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;

class QuizAttemptResolver implements QueryInterface
{
    public function __construct(
        private readonly QuizAttemptRepository $repository
    ) {}

    public function resolveAttemptsForUser(Argument $args): array
    {
        return $this->repository->findBy(
            ['user' => $args['id']],
            ['attemptedAt' => 'DESC']
        );
    }

    public function resolveAttempt(Argument $args): ?QuizAttempt
    {
        return $this->repository->findOneBy(['id' => $args['id']]);
    }
}
