<?php

namespace App\Repository;

use App\Dto\QuizDto;
use App\Dto\SearchCriteria\QuizSearchCriteriaDto;
use App\Entity\Quiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Overblog\GraphQLBundle\Error\UserError;
use Symfony\Component\HttpFoundation\JsonResponse;

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

        if ($searchCriteria->getKeywords()) {
            $qb->andWhere('LOWER(quiz.title) LIKE :title')
                ->setParameter('title', '%' . strtolower($searchCriteria->getKeywords()) . '%');
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

    public function updateQuiz(int $quizId, QuizDto $quizDto): Quiz
    {
        $quiz = $this->findOneBy(['id' => $quizId]);
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

    public function searchQuizzes(QuizSearchCriteriaDto $searchCriteria): array
    {
        if (strlen($searchCriteria->getKeywords()) < 3) {
            return $this->searchWithLike($searchCriteria->getKeywords());
        }

        return $this->fullTextSearch($searchCriteria->getKeywords());
    }

    private function searchWithLike(string $keywords): array
    {
        return $this->createQueryBuilder('quiz')
            ->select('quiz.id')
            ->leftJoin('quiz.tags', 'tag')
            ->orWhere('LOWER(quiz.title) LIKE :keywords')
            ->setParameter('keywords', '%' . strtolower($keywords) . '%')
            ->orWhere('LOWER(quiz.description) LIKE :keywords')
            ->setParameter('keywords', '%' . strtolower($keywords) . '%')
            ->orWhere('LOWER(tag.displayName) LIKE :keywords')
            ->setParameter('keywords', '%' . strtolower($keywords) . '%')
            ->getQuery()
            ->getResult();
    }

    private function fullTextSearch(string $keywords): array
    {
        $conn = $this->entityManager->getConnection();

        $sql = "
            SELECT DISTINCT q.id
            FROM quiz q
            LEFT JOIN tag_quiz tq ON q.id = tq.quiz_id
            LEFT JOIN tag t ON t.id = tq.tag_id
            WHERE MATCH(q.title) AGAINST (:keywords IN NATURAL LANGUAGE MODE)
            OR MATCH(q.description) AGAINST (:keywords IN NATURAL LANGUAGE MODE)
            OR MATCH(t.display_name) AGAINST (:keywords IN NATURAL LANGUAGE MODE)
        ";

        return $conn->executeQuery($sql, ['keywords' => $keywords])->fetchAllAssociative();
    }

    public function filterByTags(array $tagIds): array
    {
        return $this->createQueryBuilder('quiz')
            ->distinct()
            ->innerJoin('quiz.tags', 'tag')
            ->where('tag.id IN (:tagIds)')
            ->setParameter('tagIds', $tagIds)
            ->getQuery()
            ->getResult();
    }
}
