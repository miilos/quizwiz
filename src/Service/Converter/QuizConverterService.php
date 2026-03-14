<?php

namespace App\Service\Converter;

use App\Dto\QuizDto;
use App\Dto\TagDto;
use App\Entity\EntityInterface;
use App\Entity\Question;
use App\Entity\Quiz;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class QuizConverterService
{
    public function __construct(
        private QuestionConverterService $questionConverter,
    ) {}

    public function entityToDto(Quiz $quiz): QuizDto
    {
        $quizDto = (new QuizDto())
            ->setId($quiz->getId())
            ->setTitle($quiz->getTitle())
            ->setDescription($quiz->getDescription());

        if ($quiz->getTags()) {
            $tagDtos = [];
            foreach ($quiz->getTags() as $tag) {
                $tagDtos[] = (new TagDto())
                    ->setId($tag->getId())
                    ->setName($tag->getName())
                    ->setDisplayName($tag->getDisplayName());
            }

            $quizDto->setTags($tagDtos);
        }

        $questionDtos = [];
        /** @var Question $question */
        foreach ($quiz->getQuestions() as $question) {
            $questionDtos[] = $this->questionConverter->entityToDto($question);
        }

        $quizDto
            ->setQuestions($questionDtos)
            ->setAuthor($quiz->getAuthor())
            ->setCreatedAt($quiz->getCreatedAt())
            ->setFurtherReading($quiz->getFurtherReading());

        return $quizDto;
    }

    public function entityArrayToDtoArray(array $entityArray): array
    {
        $dtoArray = [];

        foreach ($entityArray as $entity) {
            $dtoArray[] = $this->entityToDto($entity);
        }

        return $dtoArray;
    }
}
