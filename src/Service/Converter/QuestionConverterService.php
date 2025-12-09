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
        $questionDto = new QuestionDto();
        $questionDto->setText($data['text'] ?? null);
        $questionDto->setOptions($data['options'] ?? null);

        if (!isset($data['correctAnswer'])) {
            $questionDto->setCorrectAnswer([]);
        }
        elseif (!is_array($data['correctAnswer'])) {
            $questionDto->setCorrectAnswer([$data['correctAnswer']]);
        }
        else {
            $questionDto->setCorrectAnswer($data['correctAnswer']);
        }

        $questionDto->setQuiz($data['quiz'] ?? null);
        $questionDto->setType($data['type'] ?? null);
        $questionDto->setExplanation($data['explanation'] ?? null);
        $questionDto->setPosition($data['position'] ?? null);

        // throws exception if it doesn't pass validation
        self::validateDto($questionDto, $this->validator);

        return $questionDto;
    }

    /**
     * @param Question $question
     */
    public function entityToDto(EntityInterface $question): QuestionDto
    {
        $questionDto = new QuestionDto();
        $questionDto->setText($question->getText());
        $questionDto->setOptions($question->getOptions());
        $questionDto->setQuiz($question->getQuiz());
        $questionDto->setCorrectAnswer($question->getCorrectAnswer());
        $questionDto->setPosition($question->getPosition());
        $questionDto->setType($question->getType()->value);
        $questionDto->setExplanation($question->getExplanation());

        return $questionDto;
    }
}
