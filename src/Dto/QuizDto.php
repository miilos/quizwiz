<?php

namespace App\Dto;

use App\Entity\User;
use DateTimeImmutable;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class QuizDto implements EntityDtoInterface
{
    public function __construct(
        #[Groups(['full_quiz_data'])]
        private ?int $id = null,

        #[Assert\NotBlank(message: 'You must title your quiz.')]
        #[Groups(['full_quiz_data'])]
        private ?string $title = null,

        #[Groups(['full_quiz_data'])]
        private ?string $description = null,

        /** @var TagDto[] $tags */
        #[Groups(['full_quiz_data'])]
        private array $tags = [],

        /** @var QuestionDto[] $questions */
        #[Groups(['full_quiz_data'])]
        private array $questions = [],

        #[Groups(['full_quiz_data'])]
        private ?User $author = null,

        #[Groups(['full_quiz_data'])]
        private ?DateTimeImmutable $createdAt = null,

        #[Groups(['full_quiz_data'])]
        private ?string $furtherReading = null,
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

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setTags(array $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getFurtherReading(): ?string
    {
        return $this->furtherReading;
    }

    public function setFurtherReading(?string $furtherReading): self
    {
        $this->furtherReading = $furtherReading;

        return $this;
    }
}
