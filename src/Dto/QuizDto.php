<?php

namespace App\Dto;

use App\Entity\User;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class QuizDto implements ResourceInterface
{
    public function __construct(
        #[Assert\NotBlank(message: 'You must title your quiz.')]
        #[Groups(['full_quiz_data'])]
        private ?string $title = null,

        #[Groups(['full_quiz_data'])]
        private ?string $description = null,

        /** @var QuestionDto[] $questions */
        #[Assert\Count(min: 1, minMessage: 'You must have at least one question.')]
        #[Groups(['full_quiz_data'])]
        private array $questions = [],

        #[Groups(['full_quiz_data'])]
        private ?User $user = null,
    ) {}

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getQuestions(): array
    {
        return $this->questions;
    }

    public function setQuestions(array $questions): void
    {
        $this->questions = $questions;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }
}
