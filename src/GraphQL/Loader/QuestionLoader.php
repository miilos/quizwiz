<?php

namespace App\GraphQL\Loader;

use App\Repository\QuestionRepository;

class QuestionLoader
{
    public function __construct(
        private readonly QuestionRepository $questionRepository,
    ) {}

    public function all(array $quizIds): array
    {
        $questions = $this->questionRepository->findBy(['quiz' => $quizIds]);

        $questionsMapped = [];
        foreach ($questions as $question) {
            $questionsMapped[$question->getQuiz()->getId()][] = $question;
        }

        return array_map(
            static fn($quizId) => $questionsMapped[$quizId] ?? [],
            $quizIds
        );
    }
}
