<?php

namespace App\Controller;

use App\Dto\SearchCriteria\QuizSearchCriteriaDto;
use App\Repository\QuizRepository;
use App\Service\Quiz\QuizManagerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

class QuizController extends AbstractController
{
    use LoggedInUserAwareTrait;

    public function __construct(
        private QuizManagerService $quizManager,
        private QuizRepository $quizRepository,
        private Security $security,
    ) {}

    #[Route('/api/quizzes', methods: ['GET'])]
    public function getQuizzes(
        #[MapQueryString] QuizSearchCriteriaDto $searchCriteria,
    ): JsonResponse
    {
        $results = $this->quizManager->search($searchCriteria);

        return $this->json([
            'status' => 'success',
            'count' => count($results),
            'data' => [
                'quizzes' => $results,
            ],
        ], context: ['groups' => 'full_quiz_data']);
    }

    #[Route('/api/quizzes/{id}', methods: ['GET'])]
    public function getQuiz(
        int $id
    ): JsonResponse
    {
        $quiz = $this->quizManager->getById($id);

        return $this->json([
            'status' => 'success',
            'data' => [
                'quiz' => $quiz,
            ]
        ], context: ['groups' => 'full_quiz_data']);
    }

    #[Route('/api/quizzes', methods: ['POST'])]
    public function createQuiz(
        Request $request,
        DecoderInterface $decoder,
    ): JsonResponse
    {
        $user = $this->getLoggedInUser($this->security);

        $reqData = $decoder->decode($request->getContent(), 'json');
        $reqData['user'] = $user;

        $quiz = $this->quizManager->create($reqData);

        return $this->json([
            'status' => 'success',
            'data' => [
                'quiz' => $quiz
            ]
        ], context: ['groups' => ['full_quiz_data']]);
    }

    #[Route('/api/quizzes/{id}', methods: ['PATCH'])]
    public function updateQuiz(
        Request $request,
        DecoderInterface $decoder,
        int $id,
    ): JsonResponse
    {
        $updateData = $decoder->decode($request->getContent(), 'json');

        $quiz = $this->quizManager->update($id, $updateData);

        return $this->json([
            'status' => 'success',
            'data' => [
                'quiz' => $quiz,
            ]
        ], context: ['groups' => ['full_quiz_data']]);
    }

    #[Route('/api/quizzes/{id}', methods: ['DELETE'])]
    public function deleteQuiz(
        int $id
    ): JsonResponse
    {
        $this->quizManager->delete($id);
        return $this->json([], Response::HTTP_NO_CONTENT);
    }
}
