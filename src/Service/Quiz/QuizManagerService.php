<?php

namespace App\Service\Quiz;

use App\Dto\QuizDto;
use App\Repository\QuizRepository;
use App\Service\Converter\QuizConverterService;

class QuizManagerService implements ResourceManagerInterface
{
    public function __construct(
        private QuizRepository $quizRepository,
        private QuestionManagerService $questionManager,
        private QuizConverterService $quizConverter,
    ) {}

    public function create(array $data): QuizDto
    {
        // write the quiz into the db first since the
        // questions require a quiz object
        $quizDto = $this->quizConverter->toDto($data);
        $quiz = $this->quizRepository->createQuiz($quizDto);

        // create the question dtos
        $questionDtos = [];
        foreach ($data['questions'] as $question) {
            $question['quiz'] = $quiz;
            $questionDto = $this->questionManager->create($question);
            $questionDtos[] = $questionDto;
        }

        $quizDto->setQuestions($questionDtos);

        return $quizDto;
    }
}
