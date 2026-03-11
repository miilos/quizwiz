<?php

namespace App\GraphQL\Resolver;

use App\Entity\Quiz;
use App\Entity\User;
use App\Service\QuizService;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;

class QuizResolver implements QueryInterface
{
    public function __construct(
        private readonly QuizService $quizService,
    ) {}

    public function resolveQuizzes(): array
    {
        return $this->quizService->getAllQuizzes();
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
        return $this->quizService->getQuizById($args->offsetGet('id'));
    }

    public function resolveTags(Quiz $quiz): ?array
    {
        return $quiz->getTags()->toArray();
    }

    public function resolveQuizSearch(Argument $args): array
    {
        $query = $args->offsetGet('searchParams')['keywords'] ?? '';
        return $this->quizService->searchQuizzes($query);
    }

    public function resolveFilterByTags(Argument $args): array
    {
        $tagIds = $args->offsetGet('tagIds');
        return $this->quizService->filterByTags($tagIds);
    }
}
