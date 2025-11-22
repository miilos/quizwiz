<?php

namespace App\Repository;

use App\Dto\QuizDto;
use App\Dto\SearchCriteria\QuizSearchCriteriaDto;
use App\Entity\Quiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
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

    public function findOneById(int $id): ?Quiz
    {
        return $this->createQueryBuilder('quiz')
            ->where('quiz.id = :id')
            ->setParameter('id', $id)
            ->leftJoin('quiz.questions', 'questions')
            ->addSelect('questions')
            ->leftJoin('quiz.attempts', 'attempts')
            ->addSelect('attempts')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function createSearch(QuizSearchCriteriaDto $searchCriteria): QueryBuilder
    {
        $qb = $this->createQueryBuilder('quiz')
            ->leftJoin('quiz.questions', 'questions')
            ->addSelect('questions')
            ->leftJoin('quiz.attempts', 'attempts')
            ->addSelect('attempts');

        if ($searchCriteria->getTitle()) {
            $qb->andWhere('LOWER(quiz.title) LIKE :title')
                ->setParameter('title', '%' . strtolower($searchCriteria->getTitle()) . '%');
        }

        return $qb;
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

    public function updateQuiz(Quiz $quiz, QuizDto $quizDto): Quiz
    {
        $quiz->setTitle($quizDto->getTitle());
        $quiz->setDescription($quizDto->getDescription());

        $this->entityManager->persist($quiz);
        $this->entityManager->flush();
        return $quiz;
    }

    public function deleteQuiz(Quiz $quiz): void
    {
        $this->entityManager->remove($quiz);
        $this->entityManager->flush();
    }
}
