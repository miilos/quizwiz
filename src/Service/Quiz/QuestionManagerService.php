<?php

namespace App\Service\Quiz;

use App\Dto\QuestionDto;
use App\Repository\QuestionRepository;
use App\Service\Converter\QuestionConverterService;

class QuestionManagerService implements ResourceManagerInterface
{
    public function __construct(
        private QuestionRepository $questionRepository,
        private QuestionConverterService $questionConverter,
    ) {}

    public function create(array $data): QuestionDto
    {
        $questionDto = $this->questionConverter->toDto($data);
        $this->questionRepository->createQuestion($questionDto);
        return $questionDto;
    }
}
