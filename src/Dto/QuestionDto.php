<?php

namespace App\Dto;

use App\Entity\Enum\QuestionTypes;
use App\Entity\Quiz;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class QuestionDto implements EntityDtoInterface
{
    public function __construct(
        #[Groups(['full_quiz_data'])]
        private ?int $id = null,

        #[Assert\NotBlank(
            message: 'You must define question text.',
            groups: ['Default', 'Update'],
        )]
        #[Groups(['full_quiz_data'])]
        private ?string $text = null,

        #[Assert\Count(
            min: 2,
            minMessage: 'A question must have at least two options.',
            groups: ['Default', 'Update'],
        )]
        #[Groups(['full_quiz_data'])]
        private array $options = [],

        private ?Quiz $quiz = null,

        #[Assert\Count(
            min: 1,
            minMessage: 'A question must have at least one correct answer.',
            groups: ['Default', 'Update'],
        )]
        #[Groups(['full_quiz_data'])]
        private array $correctAnswer = [],

        #[Groups(['full_quiz_data'])]
        private ?int $position = null,

        #[Assert\Choice(
            callback: [QuestionTypes::class, 'getValues'], message: 'Not a valid question type.',
            groups: ['Default']
        )]
        #[Groups(['full_quiz_data'])]
        private ?string $type = null,

        #[Groups(['full_quiz_data'])]
        private ?string $explanation = null,
    ) {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): self
    {
        $this->quiz = $quiz;

        return $this;
    }

    public function getCorrectAnswer(): array
    {
        return $this->correctAnswer;
    }

    public function setCorrectAnswer(array $correctAnswer): self
    {
        $this->correctAnswer = $correctAnswer;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getExplanation(): ?string
    {
        return $this->explanation;
    }

    public function setExplanation(?string $explanation): self
    {
        $this->explanation = $explanation;

        return $this;
    }
}
