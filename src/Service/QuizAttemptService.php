<?php

namespace App\Service;

use App\Controller\LoggedInUserAwareTrait;
use App\Dto\QuizAnswerDto;
use App\Entity\QuizAttempt;
use App\Repository\QuestionRepository;
use App\Repository\QuizRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class QuizAttemptService
{
    use LoggedInUserAwareTrait;

    public function __construct(
        private readonly QuizRepository $quizRepository,
        private readonly QuestionRepository $questionRepository,
        private readonly Security $security,
        private readonly EntityManagerInterface $entityManager,
        private readonly QuizAnswerCheckerService $quizAnswerCheckerService,
    ) {}

    public function createQuizAttempt(array $attemptInput): QuizAttempt
    {
        $quiz = $this->quizRepository->findOneBy(['id' => $attemptInput['quizId']]);
        $user = $this->getLoggedInUser($this->security);

        $attempt = (new QuizAttempt())
            ->setQuiz($quiz)
            ->setUser($user)
            ->setAttemptedAt(new DateTimeImmutable());

        $answers = array_map(
            static fn($answer) => QuizAnswerDto::fromArray($answer),
            $attemptInput['answers']
        );

        $results = $this->quizAnswerCheckerService->checkAnswers($answers, $quiz);

        $attempt
            ->setAnswers($answers)
            ->setCorrectAnswerCount($results->getCorrectAnswerCount())
            ->setIncorrectAnswerCount($results->getIncorrectAnswerCount())
            ->setPercentageScore($results->getPercentageScore());

        $this->entityManager->persist($attempt);
        $this->entityManager->flush();

        return $attempt;
    }
}
