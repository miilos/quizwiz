<?php

namespace App\Controller;

use App\Exception\AuthException;
use App\Service\OpenAi\QuestionAiGeneratorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

class PromptController extends AbstractController
{
    use LoggedInUserAwareTrait;

    public function __construct(
        private Security $security,
    ) {}

    #[Route('/api/prompt/questions', methods: ['POST'])]
    public function prompt(
        Request $request,
        DecoderInterface $decoder,
        QuestionAiGeneratorService $questionAiGenerator,
    ): JsonResponse
    {
        $user = $this->getLoggedInUser($this->security, 'You must be logged in to access AI features.');

        $reqData = $decoder->decode($request->getContent(), 'json');

        if (!isset($reqData['prompt'])) {
            throw new BadRequestHttpException('You must specify a prompt!', code: Response::HTTP_BAD_REQUEST);
        }

        $prompt = $reqData['prompt'];

        $question = $questionAiGenerator->generateQuestion($prompt, $user->getUserIdentifier());

        return $this->json([
            'status' => 'success',
            'data' => [
                'question' => $question,
            ]
        ]);
    }
}
