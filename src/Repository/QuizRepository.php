<?php

namespace App\Repository;

use App\Dto\QuizDto;
use App\Entity\Quiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Quiz>
 */
class QuizRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Quiz::class);
        $this->entityManager = $entityManager;
    }

    public function createQuiz(QuizDto $quizDto): Quiz
    {
        $quiz = new Quiz();
        $quiz->setTitle($quizDto->getTitle());
        $quiz->setDescription($quizDto->getDescription());
        $quiz->setAuthor($quizDto->getUser());
        $quiz->setCreatedAt(new \DateTimeImmutable('now'));

        $this->entityManager->persist($quiz);
        $this->entityManager->flush();
        return $quiz;
    }
}
