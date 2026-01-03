<?php

namespace App\GraphQL\Mutation;

use App\Controller\LoggedInUserAwareTrait;
use App\Dto\QuestionDto;
use App\Dto\QuizDto;
use App\Entity\Enum\QuestionTypes;
use App\Entity\Question;
use App\Entity\Quiz;
use App\Entity\Trait\EntityValidatorTrait;
use App\GraphQL\GraphQLUserErrorService;
use App\Repository\QuestionRepository;
use App\Repository\QuizRepository;
use App\Service\TagManagerService;
use DateTimeImmutable;
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
    use EntityValidatorTrait;

    public function __construct(
        private readonly Security $security,
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator,
        private readonly QuizRepository $quizRepository,
        private readonly QuestionRepository $questionRepository,
        private readonly TagManagerService  $tagManager,
    ) {}

    public function createQuiz(Argument $args): int
    {
        try {
            $quizData = $args['quiz'];
            $user = $this->getLoggedInUser($this->security);

            $quiz = (new Quiz())
                ->setTitle($quizData['title'])
                ->setDescription($quizData['description'] ?? null)
                ->setAuthor($user)
                ->setCreatedAt(new DateTimeImmutable());

            self::validate($quiz, $this->validator);

            if (isset($quizData['tags'])) {
                $tags = $this->tagManager->getOrCreateQuizTags($quizData['tags'], $quiz);

                foreach ($tags as $tag) {
                    $quiz->addTag($tag);
                    $this->entityManager->persist($tag);
                }
            }

            $position = 1;
            foreach ($quizData['questions'] as $questionData) {
                $question = (new Question())
                    ->setQuiz($quiz)
                    ->setText($questionData['text'])
                    ->setOptions($questionData['options'])
                    ->setCorrectAnswer($questionData['correctAnswer'])
                    ->setType(QuestionTypes::tryFrom($questionData['type']))
                    ->setPosition($position++)
                    ->setExplanation($questionData['explanation'] ?? null);

                self::validate($question, $this->validator);

                $this->entityManager->persist($question);
            }

            $this->entityManager->persist($quiz);
            $this->entityManager->flush();

            return $quiz->getId();
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
        catch (Throwable $e) {
            throw new UserError($e->getMessage());
        }
    }
}
