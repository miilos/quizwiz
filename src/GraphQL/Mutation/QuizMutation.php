<?php

namespace App\GraphQL\Mutation;

use App\Service\QuizService;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Overblog\GraphQLBundle\Error\UserError;
use Throwable;

class QuizMutation implements MutationInterface
{
    public function __construct(
        private readonly QuizService $quizService,
    ) {}

    public function createQuiz(Argument $args): int
    {
        try {
            $quiz = $this->quizService->createQuiz($args->offsetGet('quiz'));
            return $quiz->getId();
        }
        catch (Throwable $e) {
            throw new UserError($e->getMessage());
        }
    }

    public function updateQuiz(Argument $args): bool
    {
        try {
            $id = $args->offsetGet('id');
            $inputData = $args->offsetGet('quiz');

            $quizUpdated = $this->quizService->updateQuiz($inputData, $id);

            if (!$quizUpdated) {
                throw new UserError('No quiz found with id ' . $id);
            }

            return $quizUpdated;
        }
        catch (Throwable $e) {
            throw new UserError($e->getMessage());
        }
    }

    public function deleteQuiz(Argument $args): bool
    {
        try {
            $id = $args->offsetGet('id');

            $quizDeleted = $this->quizService->deleteQuiz($id);

            if (!$quizDeleted) {
                throw new UserError(sprintf('Quiz with id "%s" not found.', $id));
            }

            return $quizDeleted;
        }
        catch (Throwable $e) {
            throw new UserError($e->getMessage());
        }
    }
}
