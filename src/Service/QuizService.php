<?php

namespace App\Service;

use App\Dto\QuizDto;
use App\Dto\SearchCriteria\QuizSearchCriteriaDto;
use App\Entity\Quiz;
use App\Entity\Trait\EntityValidatorTrait;
use App\Entity\User;
use App\Repository\QuizRepository;
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
    ) {}

    public function getAllQuizzes(): array
    {
        $quizzes = $this->quizRepository->findAll();
        return $this->quizConverterService->entityArrayToDtoArray($quizzes);
    }

    public function getQuizById(int $id): ?QuizDto
    {
        $quiz = $this->quizRepository->findOneBy(["id" => $id]);

        if (!$quiz) {
            return null;
        }

        return $this->quizConverterService->entityToDto($quiz);
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

        $quizEntities = $this->quizRepository->findBy(['id' => $quizIds]);
        return $this->quizConverterService->entityArrayToDtoArray($quizEntities);
    }

    public function filterByTags(array $tagIds): array
    {
        $quizEntities = $this->quizRepository->filterByTags($tagIds);
        return $this->quizConverterService->entityArrayToDtoArray($quizEntities);
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

    public function updateQuiz(array $inputData, int $id): bool
    {
        $quiz = $this->quizRepository->findOneBy(["id" => $id]);

        if (!$quiz) {
            return false;
        }

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

    public function deleteQuiz(int $id): bool
    {
        $quiz = $this->quizRepository->findOneBy(["id" => $id]);

        if (!$quiz) {
            return false;
        }

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
