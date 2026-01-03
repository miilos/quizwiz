<?php

namespace App\GraphQL\Resolver;

use App\Entity\Quiz;
use App\Entity\User;
use App\Repository\QuizRepository;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;

class QuizResolver implements QueryInterface
{
    public function __construct(
        private readonly QuizRepository $quizRepository,
    ) {}

    public function resolveQuizzes(): array
    {
        return $this->quizRepository->findAll();
    }

    public function resolveQuestions(Quiz $quiz): array
    {
        return $quiz->getQuestions()->toArray();
    }

    public function resolveAuthor(Quiz $quiz): ?User
    {
        return $quiz->getAuthor();
    }

    public function resolveQuiz(Argument $args): ?Quiz
    {
        return $this->quizRepository->findOneById($args->offsetGet('id'));
    }

    public function resolveTags(Quiz $quiz): ?array
    {
        return $quiz->getTags()->toArray();
    }
}
