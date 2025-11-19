<?php

namespace App\Repository;

use App\Dto\QuestionDto;
use App\Entity\Enum\QuestionTypes;
use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Question>
 */
class QuestionRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Question::class);
        $this->entityManager = $entityManager;
    }

    public function createQuestion(QuestionDto $questionDto): Question
    {
        $question = new Question();
        $question->setQuiz($questionDto->getQuiz());
        $question->setText($questionDto->getText());
        $question->setOptions($questionDto->getOptions());
        $question->setCorrectAnswer($questionDto->getCorrectAnswer());
        $question->setPosition($questionDto->getPosition());
        $question->setType(QuestionTypes::from($questionDto->getType()));
        $question->setExplanation($questionDto->getExplanation());

        $this->entityManager->persist($question);
        $this->entityManager->flush();
        return $question;
    }
}
