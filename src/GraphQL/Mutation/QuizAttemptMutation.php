<?php

namespace App\GraphQL\Mutation;

use App\Entity\QuizAttempt;
use App\Service\QuizAttemptService;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Overblog\GraphQLBundle\Error\UserError;
use Throwable;

class QuizAttemptMutation implements MutationInterface
{
    public function __construct(
        private QuizAttemptService $quizAttemptService
    ) {}

    public function createAttempt(Argument $args): ?QuizAttempt
    {
        try {
            return $this->quizAttemptService->createQuizAttempt($args['attempt']);
        }
        catch (Throwable $e) {
            throw new UserError($e->getMessage());
        }
    }
}
