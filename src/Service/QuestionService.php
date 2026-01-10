<?php

namespace App\Service;

use App\Entity\Enum\QuestionTypes;
use App\Entity\Question;
use App\Entity\Quiz;
use App\Entity\Trait\EntityValidatorTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class QuestionService
{
    use EntityValidatorTrait;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator,
    ) {}

    public function createQuestionSet(array $questionsInput, Quiz $quiz): array
    {
        $position = 1;
        $questions = [];

        foreach ($questionsInput as $questionInput) {
            $question = (new Question())
                ->setQuiz($quiz)
                ->setText($questionInput['text'])
                ->setOptions($questionInput['options'])
                ->setCorrectAnswer($questionInput['correctAnswer'])
                ->setType(QuestionTypes::tryFrom($questionInput['type']))
                ->setPosition($position++)
                ->setExplanation($questionInput['explanation'] ?? null);

            self::validate($question, $this->validator);

            $questions[] = $question;
        }

        return $questions;
    }

    public function updateQuizQuestions(array $inputQuestions, Quiz $quiz): array
    {
        $existingQuestions = $quiz->getQuestions();
        $submittedQuestionIds = array_map(static fn($question) => $question['id'], $inputQuestions);

        $updatedQuestionList = [];
        foreach ($existingQuestions as $question) {
            if (in_array($question->getId(), $submittedQuestionIds, true)) {
                $submittedQuestionData = self::findQuestionDataById($question->getId(), $inputQuestions);

                $question
                    ->setText($submittedQuestionData['text'])
                    ->setCorrectAnswer($submittedQuestionData['correctAnswer'])
                    ->setOptions($submittedQuestionData['options'])
                    ->setExplanation($submittedQuestionData['explanation'] ?? null);

                self::validate($question, $this->validator);

                $updatedQuestionList[] = $question;
                continue;
            }

            $this->entityManager->remove($question);
        }
        $this->entityManager->flush();

        $updatedQuestionIds = array_map(static fn($question) => $question->getId(), $updatedQuestionList);
        $questionsToAddIds = array_diff($submittedQuestionIds, $updatedQuestionIds);

        foreach ($questionsToAddIds as $questionToAddId) {
            $questionToAdd = self::findQuestionDataById($questionToAddId, $inputQuestions);

            $question = (new Question())
                ->setQuiz($quiz)
                ->setText($questionToAdd['text'])
                ->setOptions($questionToAdd['options'])
                ->setCorrectAnswer($questionToAdd['correctAnswer'])
                ->setType(QuestionTypes::tryFrom($questionToAdd['type'] ?? null))
                ->setPosition(self::getNewQuestionPosition($updatedQuestionList))
                ->setExplanation($questionToAdd['explanation'] ?? null);

            self::validate($question, $this->validator);

            $updatedQuestionList[] = $question;
        }

        return $updatedQuestionList;
    }

    private static function findQuestionDataById(int $id, array $questionData): array
    {
        return array_find($questionData, function ($question) use ($id) {
            return $id === $question['id'];
        });
    }

    private static function getNewQuestionPosition(array $updatedQuestions): int
    {
        if (!$updatedQuestions) {
            return 1;
        }

        return $updatedQuestions[count($updatedQuestions) - 1]->getPosition() + 1;
    }
}
