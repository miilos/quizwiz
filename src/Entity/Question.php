<?php

namespace App\Entity;

use App\Entity\Enum\QuestionTypes;
use App\Repository\QuestionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question implements EntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['full_quiz_data'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'questions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Quiz $quiz = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['full_quiz_data'])]
    private ?string $text = null;

    #[ORM\Column(type: Types::JSON)]
    #[Groups(['full_quiz_data'])]
    private array $options = [];

    #[ORM\Column(type: Types::JSON)]
    #[Groups(['full_quiz_data'])]
    private ?array $correctAnswer = [];

    #[ORM\Column]
    #[Groups(['full_quiz_data'])]
    private ?int $position = null;

    #[ORM\Column(enumType: QuestionTypes::class)]
    #[Groups(['full_quiz_data'])]
    private ?QuestionTypes $type = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['full_quiz_data'])]
    private ?string $explanation = null;

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

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function getCorrectAnswer(): array
    {
        return $this->correctAnswer;
    }

    public function setCorrectAnswer(array $correctAnswer): static
    {
        $this->correctAnswer = $correctAnswer;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getType(): ?QuestionTypes
    {
        return $this->type;
    }

    public function setType(QuestionTypes $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getExplanation(): ?string
    {
        return $this->explanation;
    }

    public function setExplanation(?string $explanation): static
    {
        $this->explanation = $explanation;

        return $this;
    }
}
