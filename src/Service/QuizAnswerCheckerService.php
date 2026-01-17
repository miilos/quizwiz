<?php

namespace App\Service;

use App\Dto\QuizAnswerDto;
use App\Dto\QuizResultsDto;
use App\Entity\Question;
use App\Entity\Quiz;
use App\Entity\User;
use Overblog\GraphQLBundle\Error\UserError;

class QuizAnswerCheckerService
{
    /** @param QuizAnswerDto[] $answers */
    public function checkAnswers(array $answers, Quiz $quiz): QuizResultsDto
    {
        $correctAnswerNum = 0;
        $incorrectAnswerNum = 0;

        $results = new QuizResultsDto();

        foreach ($answers as $answer) {
            $question = array_find(
                $quiz->getQuestions()->toArray(),
                static function(Question $question) use ($answer) {
                    return $question->getId() === $answer->getQuestionId();
                }
            );

            if (!$question) {
                continue;
            }

            $questionAnswers = $question->getCorrectAnswer();
            $userAnswers = $answer->getAnswers();

            sort($questionAnswers);
            sort($userAnswers);

            $status = ($questionAnswers === $userAnswers)
                ? QuizAnswerDto::STATUS_CORRECT
                : QuizAnswerDto::STATUS_INCORRECT;

            if ($status === QuizAnswerDto::STATUS_CORRECT) {
                $correctAnswerNum++;
            }

            if ($status === QuizAnswerDto::STATUS_INCORRECT) {
                $incorrectAnswerNum++;
            }

            $answer->setStatus($status);
        }

        $percentageScore = round(($correctAnswerNum / $quiz->getQuestions()->count()) * 100, 2);

        $results
            ->setAnswers($answers)
            ->setCorrectAnswerCount($correctAnswerNum)
            ->setIncorrectAnswerCount($incorrectAnswerNum)
            ->setPercentageScore($percentageScore);

        return $results;
    }
}
