<?php

namespace App\Dto;

class QuizResultsDto
{
    public function __construct(
        private array $answers = [],
        private ?int $correctAnswerCount = null,
        private ?int $incorrectAnswerCount = null,
        private ?float $percentageScore = null,
    ) {}

    /**
     * @return QuizAnswerDto[]
     */
    public function getAnswers(): array
    {
        return $this->answers;
    }

    /**
     * @param QuizAnswerDto[] $answers
     * @return $this
     */
    public function setAnswers(array $answers): self
    {
        $this->answers = $answers;

        return $this;
    }

    public function getCorrectAnswerCount(): ?int
    {
        return $this->correctAnswerCount;
    }

    public function setCorrectAnswerCount(?int $correctAnswerCount): self
    {
        $this->correctAnswerCount = $correctAnswerCount;

        return $this;
    }

    public function getIncorrectAnswerCount(): ?int
    {
        return $this->incorrectAnswerCount;
    }

    public function setIncorrectAnswerCount(?int $incorrectAnswerCount): self
    {
        $this->incorrectAnswerCount = $incorrectAnswerCount;

        return $this;
    }

    public function getPercentageScore(): ?float
    {
        return $this->percentageScore;
    }

    public function setPercentageScore(?float $percentageScore): self
    {
        $this->percentageScore = $percentageScore;

        return $this;
    }
}
