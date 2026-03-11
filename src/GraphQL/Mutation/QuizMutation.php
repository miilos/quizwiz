<?php

namespace App\GraphQL\Mutation;

use App\Controller\LoggedInUserAwareTrait;
use App\Exception\AuthException;
use App\Service\QuizService;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Overblog\GraphQLBundle\Error\UserError;
use Symfony\Bundle\SecurityBundle\Security;
use Throwable;

class QuizMutation implements MutationInterface
{
    use LoggedInUserAwareTrait;

    public function __construct(
        private readonly QuizService $quizService,
        private readonly Security $security,
    ) {}

    public function createQuiz(Argument $args): int
    {
        try {
            $user = self::getLoggedInUser($this->security);

            $quiz = $this->quizService->createQuiz($args->offsetGet('quiz'), $user);
            return $quiz->getId();
        }
        catch (AuthException) {
            throw new UserError('access.denied');
        }
        catch (Throwable $e) {
            throw new UserError($e->getMessage());
        }
    }

    public function updateQuiz(Argument $args): bool
    {
        try {
            $user = self::getLoggedInUser($this->security);

            $id = $args->offsetGet('id');
            $inputData = $args->offsetGet('quiz');

            $quizUpdated = $this->quizService->updateQuiz($inputData, $id, $user);

            if (!$quizUpdated) {
                throw new UserError('No quiz found with id ' . $id);
            }

            return $quizUpdated;
        }
        catch (AuthException) {
            throw new UserError('access.denied');
        }
        catch (Throwable $e) {
            throw new UserError($e->getMessage());
        }
    }

    public function deleteQuiz(Argument $args): bool
    {
        try {
            $user = self::getLoggedInUser($this->security);

            $id = $args->offsetGet('id');

            $quizDeleted = $this->quizService->deleteQuiz($id, $user);

            if (!$quizDeleted) {
                throw new UserError(sprintf('Quiz with id "%s" not found.', $id));
            }

            return $quizDeleted;
        }
        catch (AuthException) {
            throw new UserError('access.denied');
        }
        catch (Throwable $e) {
            throw new UserError($e->getMessage());
        }
    }
}
