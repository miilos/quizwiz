<?php

namespace App\Service\Converter;

use App\Dto\QuizDto;
use App\Entity\EntityInterface;
use App\Entity\Question;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class QuizConverterService extends EntityConverter
{
    use ValidatesDtoTrait;

    public function __construct(
        private ValidatorInterface $validator,
        private QuestionConverterService $questionConverter,
    ) {}

    /** @throws UnprocessableEntityHttpException */
    public function toDto(array $data): QuizDto
    {
        $quizDto = (new QuizDto())
            ->setTitle($data['title'] ?? null)
            ->setDescription($data['description'] ?? null)
            ->setQuestions($data['questions'] ?? [])
            ->setUser($data['user'] ?? null);

        self::validateDto($quizDto, $this->validator);

        return $quizDto;
    }

    /**
     * creates a dto first in order to use the validator component
     * to validate the new data and creates the updates
     * in the db afterward
     */
    public function entityToDto(EntityInterface $quiz): QuizDto
    {
        $quizDto = (new QuizDto())
            ->setTitle($quiz->getTitle())
            ->setDescription($quiz->getDescription());

        $questionDtos = [];
        /** @var Question $question */
        foreach ($quiz->getQuestions() as $question) {
            $questionDtos[] = $this->questionConverter->entityToDto($question);
        }

        $quizDto
            ->setQuestions($questionDtos)
            ->setUser($quiz->getAuthor());

        return $quizDto;
    }
}
