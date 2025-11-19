<?php

namespace App\Controller;

use App\Exception\AuthException;
use App\Service\Quiz\QuizManagerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

class QuizController extends AbstractController
{
    #[Route('/api/quizzes', methods: ['POST'])]
    public function createQuiz(
        Request $request,
        DecoderInterface $decoder,
        Security $security,
        QuizManagerService $quizManager
    ): JsonResponse
    {
        $user = $security->getUser();

        if (!$user) {
            throw new AuthException(
                'You must be logged in to create a quiz!',
                Response::HTTP_FORBIDDEN
            );
        }

        $reqData = $decoder->decode($request->getContent(), 'json');
        $reqData['user'] = $user;

        $quiz = $quizManager->create($reqData);

        return $this->json([
            'status' => 'success',
            'data' => [
                'quiz' => $quiz
            ]
        ], context: ['groups' => ['full_quiz_data']],);
    }
}
