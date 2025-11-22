<?php

namespace App\Entity;

use App\Repository\QuizRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: QuizRepository::class)]
class Quiz implements EntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['full_quiz_data'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['full_quiz_data'])]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Groups(['full_quiz_data'])]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'quizzes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['full_quiz_data'])]
    private ?User $author = null;

    #[ORM\Column]
    #[Groups(['full_quiz_data'])]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, Question>
     */
    #[ORM\OneToMany(targetEntity: Question::class, mappedBy: 'quiz', orphanRemoval: true)]
    #[Groups(['full_quiz_data'])]
    private Collection $questions;

    /**
     * @var Collection<int, QuizAttempt>
     */
    #[ORM\OneToMany(targetEntity: QuizAttempt::class, mappedBy: 'quiz', orphanRemoval: true)]
    #[Groups(['full_quiz_data'])]
    private Collection $attempts;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->attempts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, Question>
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): static
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
            $question->setQuiz($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): static
    {
        if ($this->questions->removeElement($question)) {
            // set the owning side to null (unless already changed)
            if ($question->getQuiz() === $this) {
                $question->setQuiz(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, QuizAttempt>
     */
    public function getAttempts(): Collection
    {
        return $this->attempts;
    }

    public function addAttempt(QuizAttempt $attempt): static
    {
        if (!$this->attempts->contains($attempt)) {
            $this->attempts->add($attempt);
            $attempt->setQuiz($this);
        }

        return $this;
    }

    public function removeAttempt(QuizAttempt $attempt): static
    {
        if ($this->attempts->removeElement($attempt)) {
            // set the owning side to null (unless already changed)
            if ($attempt->getQuiz() === $this) {
                $attempt->setQuiz(null);
            }
        }

        return $this;
    }
}
