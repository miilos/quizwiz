<?php

namespace App\Entity;

use App\Dto\QuizAnswerDto;
use App\Repository\QuizAttemptRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: QuizAttemptRepository::class)]
class QuizAttempt implements EntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['full_quiz_data', 'fullUserInfo'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'attempts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Quiz $quiz = null;

    #[ORM\Column]
    #[Groups(['full_quiz_data', 'fullUserInfo'])]
    private array $answers = [];

    #[ORM\ManyToOne(inversedBy: 'quizAttempts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['full_quiz_data'])]
    private ?User $user = null;

    #[ORM\Column]
    #[Groups(['full_quiz_data', 'fullUserInfo'])]
    private ?\DateTimeImmutable $attemptedAt = null;

    #[ORM\Column]
    #[Groups(['full_quiz_data', 'fullUserInfo'])]
    private ?int $correctAnswerCount = null;

    #[ORM\Column]
    #[Groups(['full_quiz_data', 'fullUserInfo'])]
    private ?int $incorrectAnswerCount = null;

    #[ORM\Column]
    #[Groups(['full_quiz_data', 'fullUserInfo'])]
    private ?float $percentageScore = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): static
    {
        $this->quiz = $quiz;

        return $this;
    }

    /** @return QuizAnswerDto[] */
    public function getAnswers(): array
    {
        return array_map(
            static fn($answer) => QuizAnswerDto::fromArray($answer),
            $this->answers
        );
    }

    /** @param QuizAnswerDto[] $answers */
    public function setAnswers(array $answers): static
    {
        $this->answers = array_map(
            static fn(QuizAnswerDto $answer) => $answer->toArray(),
            $answers
        );

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): static
    {
        $this->score = $score;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getAttemptedAt(): ?\DateTimeImmutable
    {
        return $this->attemptedAt;
    }

    public function setAttemptedAt(\DateTimeImmutable $attemptedAt): static
    {
        $this->attemptedAt = $attemptedAt;

        return $this;
    }

    public function getCorrectAnswerCount(): ?int
    {
        return $this->correctAnswerCount;
    }

    public function setCorrectAnswerCount(int $correctAnswerCount): static
    {
        $this->correctAnswerCount = $correctAnswerCount;

        return $this;
    }

    public function getIncorrectAnswerCount(): ?int
    {
        return $this->incorrectAnswerCount;
    }

    public function setIncorrectAnswerCount(int $incorrectAnswerCount): static
    {
        $this->incorrectAnswerCount = $incorrectAnswerCount;

        return $this;
    }

    public function getPercentageScore(): ?float
    {
        return $this->percentageScore;
    }

    public function setPercentageScore(float $percentageScore): static
    {
        $this->percentageScore = $percentageScore;

        return $this;
    }
}
