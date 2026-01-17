<?php

namespace App\Dto;

class QuizAnswerDto
{
    public const STATUS_CORRECT = 'correct';
    public const STATUS_INCORRECT = 'incorrect';
    private const ALLOWED_STATUSES = [
        self::STATUS_CORRECT,
        self::STATUS_INCORRECT,
    ];

    public function __construct(
        private ?int $questionId = null,
        private array $answers = [],
        private ?string $status = null,
    ) {}

    public function getQuestionId(): ?int
    {
        return $this->questionId;
    }

    public function setQuestionId(?int $questionId): self
    {
        $this->questionId = $questionId;

        return $this;
    }

    public function getAnswers(): array
    {
        return $this->answers;
    }

    public function setAnswers(array $answers): self
    {
        $this->answers = $answers;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        if (!in_array($status, self::ALLOWED_STATUSES, true)) {
            $this->status = null;
            return $this;
        }

        $this->status = $status;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'questionId' => $this->questionId,
            'answers' => $this->answers,
            'status' => $this->status ?? '',
        ];
    }

    public static function fromArray(array $data): self
    {
        return (new self())
            ->setQuestionId($data['questionId'])
            ->setAnswers($data['answers'])
            ->setStatus($data['status'] ?? null);
    }
}
