<?php

namespace App\Service\Converter;

use App\Dto\QuizDto;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class QuizConverterService extends EntityConverter
{
    public function __construct(
        ValidatorInterface $validator
    ) {
        parent::__construct($validator);
    }

    public function toDto(array $data): QuizDto
    {
        $quizDto = new QuizDto();
        $quizDto->setTitle($data['title'] ?? null);
        $quizDto->setDescription($data['description'] ?? null);
        $quizDto->setQuestions($data['questions'] ?? []);
        $quizDto->setUser($data['user'] ?? null);

        // throws exception if it doesn't pass validation
        $this->validateDto($quizDto);

        return $quizDto;
    }
}
