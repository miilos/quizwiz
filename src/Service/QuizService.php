<?php

namespace App\Service;

use App\Dto\QuizDto;
use App\Dto\SearchCriteria\QuizSearchCriteriaDto;
use App\Entity\Quiz;
use App\Entity\Trait\EntityValidatorTrait;
use App\Entity\User;
use App\Repository\QuizRepository;
use App\Service\Auth\AuthorizationService;
use App\Service\Converter\QuizConverterService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class QuizService
{
    use EntityValidatorTrait;

    public function __construct(
        private readonly QuizRepository $quizRepository,
        private readonly QuizConverterService $quizConverterService,
        private readonly ValidatorInterface $validator,
        private readonly EntityManagerInterface $entityManager,
        private readonly TagService $tagService,
        private readonly QuestionService $questionService,
        private readonly AuthorizationService $authorizationService,
    ) {}

    public function getAllQuizzes(): array
    {
        return $this->quizRepository->findAll();
    }

    public function getQuizById(int $id): ?Quiz
    {
        $quiz = $this->quizRepository->findOneBy(["id" => $id]);

        if (!$quiz) {
            return null;
        }

        return $quiz;
    }

    public function searchQuizzes(string $query): array
    {
        $criteria = (new QuizSearchCriteriaDto())
            ->setKeywords($query);

        $result = $this->quizRepository->searchQuizzes($criteria);
        $quizIds = array_unique(array_column($result, 'id'));

        if (empty($quizIds)) {
            return [];
        }

        return $this->quizRepository->findBy(['id' => $quizIds]);
    }

    public function filterByTags(array $tagIds): array
    {
        return $quizEntities = $this->quizRepository->filterByTags($tagIds);
    }

    public function createQuiz(array $inputData, User $user): Quiz
    {
        $quiz = (new Quiz())
            ->setTitle($inputData['title'])
            ->setDescription($inputData['description'] ?? null)
            ->setAuthor($user)
            ->setCreatedAt(new DateTimeImmutable());

        self::validate($quiz, $this->validator);

        if (isset($inputData['tags'])) {
            $tags = $this->tagService->getOrCreateQuizTags($inputData['tags'], $quiz);

            foreach ($tags as $tag) {
                $quiz->addTag($tag);
                $this->entityManager->persist($tag);
            }
        }

        $questions = $this->questionService->createQuestionSet($inputData['questions'], $quiz);
        foreach ($questions as $question) {
            $this->entityManager->persist($question);
        }

        $this->entityManager->persist($quiz);
        $this->entityManager->flush();

        return $quiz;
    }

    public function updateQuiz(array $inputData, int $id, User $user): bool
    {
        $quiz = $this->quizRepository->findOneBy(["id" => $id]);

        if (!$quiz) {
            return false;
        }

        $this->authorizationService->authorizeQuizActions($user, $quiz);

        $quiz
            ->setTitle($inputData['title'])
            ->setDescription($inputData['description'] ?? null);

        if (isset($inputData['tags'])) {
            $tags = $this->tagService->updateQuizTags($inputData['tags'], $quiz);

            foreach ($tags as $tag) {
                $quiz->addTag($tag);
                $this->entityManager->persist($tag);
            }
        }

        $updatedQuestions = $this->questionService->updateQuizQuestions($inputData['questions'], $quiz);
        foreach ($updatedQuestions as $question) {
            $this->entityManager->persist($question);
        }

        $this->entityManager->flush();
        return true;
    }

    public function deleteQuiz(int $id, User $user): bool
    {
        $quiz = $this->quizRepository->findOneBy(["id" => $id]);

        if (!$quiz) {
            return false;
        }

        $this->authorizationService->authorizeQuizActions($user, $quiz);

        foreach ($quiz->getTags() as $tag) {
            $tag->removeQuiz($quiz);
        }

        foreach ($quiz->getQuestions() as $question) {
            $this->entityManager->remove($question);
        }

        $this->entityManager->remove($quiz);
        $this->entityManager->flush();

        return true;
    }
}
