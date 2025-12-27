<?php

namespace App\GraphQL\Mutation;

use App\Controller\LoggedInUserAwareTrait;
use App\Dto\QuestionDto;
use App\Dto\QuizDto;
use App\GraphQL\GraphQLUserErrorService;
use App\Repository\QuestionRepository;
use App\Repository\QuizRepository;
use Doctrine\ORM\EntityManagerInterface;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Overblog\GraphQLBundle\Error\UserError;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

class QuizMutation implements MutationInterface
{
    use LoggedInUserAwareTrait;

    public function __construct(
        private readonly Security $security,
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator,
        private readonly QuizRepository $quizRepository,
        private readonly QuestionRepository $questionRepository,
    ) {}

    public function createQuiz(Argument $args): int
    {
        try {
            $quizData = $args['quiz'];
            $user = $this->getLoggedInUser($this->security);

            $questionDtos = [];
            $questionPosition = 1;
            foreach ($quizData['questions'] as $question) {
                $questionDto = (new QuestionDto())
                    ->setText($question['text'])
                    ->setOptions($question['options'])
                    ->setCorrectAnswer($question['correctAnswer'])
                    ->setPosition($questionPosition)
                    ->setType($question['type'])
                    ->setExplanation($question['explanation']);

                $violations = $this->validator->validate($questionDto);
                if (count($violations) > 0) {
                    GraphQLUserErrorService::throwValidationFailedUserError($violations);
                }

                $questionDtos[] = $questionDto;
                $questionPosition++;
            }

            $quizDto = (new QuizDto())
                ->setTitle($quizData['title'])
                ->setDescription($quizData['description'] ?? null)
                ->setQuestions($questionDtos)
                ->setUser($user);

            $violations = $this->validator->validate($quizDto);
            if (count($violations) > 0) {
                GraphQLUserErrorService::throwValidationFailedUserError($violations);
            }

            $quizEntity = $this->quizRepository->createQuiz($quizDto);

            foreach ($questionDtos as $questionDto) {
                $questionDto->setQuiz($quizEntity);
                $this->questionRepository->createQuestion($questionDto);
            }

            return $quizEntity->getId();
        }
        catch (Throwable $e) {
            throw new UserError($e->getMessage());
        }
    }

    public function updateQuiz(Argument $args): bool
    {
        try {
            $quiz = $this->quizRepository->findOneById($args['id']);
            $quizArgs = $args['quiz'];

            foreach ($quizArgs['questions'] as $question) {
                $questionDto = (new QuestionDto())
                    ->setText($question['text'])
                    ->setOptions($question['options'])
                    ->setCorrectAnswer($question['correctAnswer'])
                    ->setExplanation($question['explanation']);

                $violations = $this->validator->validate($questionDto, groups: ['Update']);
                if (count($violations) > 0) {
                    GraphQLUserErrorService::throwValidationFailedUserError($violations);
                }

                $this->questionRepository->updateQuestion($question['id'], $questionDto);
            }

            $quizDto = (new QuizDto())
                ->setTitle($quizArgs['title'])
                ->setDescription($quizArgs['description'] ?? null);

            $violations = $this->validator->validate($quizDto);
            if (count($violations) > 0) {
                GraphQLUserErrorService::throwValidationFailedUserError($violations);
            }

            $this->quizRepository->updateQuiz($quiz->getId(), $quizDto);

            return true;
        }
        catch (Throwable $e) {
            throw new UserError($e->getMessage());
        }
    }

    public function deleteQuiz(Argument $args): bool
    {
        try {
            $quiz = $this->quizRepository->findOneBy(['id' => $args['id']]);

            foreach ($quiz->getQuestions() as $question) {
                $this->questionRepository->deleteQuestion($question);
            }

            $this->quizRepository->deleteQuiz($quiz);

            return true;
        }
        catch (Throwable $e) {
            throw new UserError($e->getMessage());
        }
    }
}
