<?php

namespace App\Service\Quiz;

use App\Dto\QuizDto;
use App\Dto\SearchCriteria\QuizSearchCriteriaDto;
use App\Entity\Quiz;
use App\Exception\ApiResourceNotFoundException;
use App\Repository\QuizRepository;
use App\Service\Converter\QuizConverterService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class QuizManagerService implements ResourceManagerInterface
{
    private const UPDATEABLE_FIELDS = [
        'title',
        'description',
    ];

    public function __construct(
        private readonly QuizRepository $quizRepository,
        private readonly QuestionManagerService $questionManager,
        private readonly QuizConverterService $quizConverter,
        private readonly ValidatorInterface $validator,
    ) {}

    public function search(QuizSearchCriteriaDto $searchCriteria): array
    {
        $qb = $this->quizRepository->createSearch($searchCriteria);
        return $qb->getQuery()->getResult();
    }

    public function getById(int $id): Quiz
    {
        $quiz = $this->quizRepository->findOneById($id);

        if (!$quiz) {
            throw new ApiResourceNotFoundException(
                sprintf('Quiz with id: %s not found.', $id),
                Response::HTTP_NOT_FOUND
            );
        }

        return $quiz;
    }

    public function create(array $data): QuizDto
    {
        // creates dtos from the data so they can be easily validated

        // write the quiz into the db first since the
        // questions require a quiz object
        $quizDto = $this->quizConverter->toDto($data);
        $quiz = $this->quizRepository->createQuiz($quizDto);

        // create the question dtos
        $questionDtos = [];
        foreach ($data['questions'] as $question) {
            $question['quiz'] = $quiz;
            $questionDto = $this->questionManager->create($question);
            $questionDtos[] = $questionDto;
        }

        $quizDto->setQuestions($questionDtos);

        return $quizDto;
    }

    public function update(int $id, array $data): Quiz
    {
        $quiz = $this->getById($id);
        $quizDto = $this->quizConverter->entityToDto($quiz);

        foreach (self::UPDATEABLE_FIELDS as $field) {
            if (isset($data[$field])) {
                $setter = 'set'.ucfirst($field);
                $quizDto->$setter($data[$field]);
            }
        }

        $this->quizConverter::validateDto($quizDto, $this->validator);

        $quiz = $this->quizRepository->updateQuiz($quiz, $quizDto);
        return $quiz;
    }

    public function delete(int $id): void
    {
        $quiz = $this->getById($id);
        $this->quizRepository->deleteQuiz($quiz);
    }
}
