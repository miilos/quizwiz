<?php

namespace App\Service\Converter;

use App\Dto\QuestionDto;
use App\Entity\EntityInterface;
use App\Entity\Question;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class QuestionConverterService extends EntityConverter
{
    use ValidatesDtoTrait;

    public function __construct(
        private ValidatorInterface $validator,
    ) {}

    public function toDto(array $data): QuestionDto
    {
        $questionDto = (new QuestionDto())
            ->setText($data['text'] ?? null)
            ->setOptions($data['options'] ?? null);

        if (!isset($data['correctAnswer'])) {
            $questionDto->setCorrectAnswer([]);
        }
        elseif (!is_array($data['correctAnswer'])) {
            $questionDto->setCorrectAnswer([$data['correctAnswer']]);
        }
        else {
            $questionDto->setCorrectAnswer($data['correctAnswer']);
        }

        $questionDto->setQuiz($data['quiz'] ?? null)
            ->setType($data['type'] ?? null)
            ->setExplanation($data['explanation'] ?? null)
            ->setPosition($data['position'] ?? null);

        // throws exception if it doesn't pass validation
        self::validateDto($questionDto, $this->validator);

        return $questionDto;
    }

    /**
     * @param Question $question
     */
    public function entityToDto(EntityInterface $question): QuestionDto
    {
        return (new QuestionDto())
            ->setText($question->getText())
            ->setOptions($question->getOptions())
            ->setQuiz($question->getQuiz())
            ->setCorrectAnswer($question->getCorrectAnswer())
            ->setPosition($question->getPosition())
            ->setType($question->getType()->value)
            ->setExplanation($question->getExplanation());
    }
}
