<?php

namespace App\Dto;

use App\Entity\User;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class QuizDto implements EntityDtoInterface
{
    public function __construct(
        #[Assert\NotBlank(message: 'You must title your quiz.')]
        #[Groups(['full_quiz_data'])]
        private ?string $title = null,

        #[Groups(['full_quiz_data'])]
        private ?string $description = null,

        /** @var QuestionDto[] $questions */
        #[Groups(['full_quiz_data'])]
        private array $questions = [],

        #[Groups(['full_quiz_data'])]
        private ?User $user = null,
    ) {}

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getQuestions(): array
    {
        return $this->questions;
    }

    public function setQuestions(array $questions): self
    {
        $this->questions = $questions;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
