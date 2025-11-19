<?php

namespace App\Dto;

use App\Entity\Enum\QuestionTypes;
use App\Entity\Quiz;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class QuestionDto implements ResourceInterface
{
    public function __construct(
        #[Assert\NotBlank(message: 'You must define question text.')]
        #[Groups(['full_quiz_data'])]
        private ?string $text = null,

        #[Assert\Count(min: 2, minMessage: 'A question must have at least two options.')]
        #[Groups(['full_quiz_data'])]
        private array $options = [],

        private ?Quiz $quiz = null,

        #[Assert\Count(min: 1, minMessage: 'A question must have at least one correct answer.')]
        #[Groups(['full_quiz_data'])]
        private array $correctAnswer = [],

        #[Groups(['full_quiz_data'])]
        private ?int $position = null,

        #[Assert\Choice(callback: [QuestionTypes::class, 'getValues'], message: 'Not a valid question type.')]
        #[Groups(['full_quiz_data'])]
        private ?string $type = null,

        #[Groups(['full_quiz_data'])]
        private ?string $explanation = null,
    ) {}

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): void
    {
        $this->text = $text;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): void
    {
        $this->quiz = $quiz;
    }

    public function getCorrectAnswer(): array
    {
        return $this->correctAnswer;
    }

    public function setCorrectAnswer(array $correctAnswer): void
    {
        $this->correctAnswer = $correctAnswer;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getExplanation(): ?string
    {
        return $this->explanation;
    }

    public function setExplanation(?string $explanation): void
    {
        $this->explanation = $explanation;
    }
}
