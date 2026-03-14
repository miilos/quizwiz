<?php

namespace App\Controller;

use App\Service\OpenAi\QuestionAiGeneratorService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

#[OA\Tag(name: 'AI')]
class PromptController extends AbstractController
{
    use LoggedInUserAwareTrait;

    public function __construct(
        private Security $security,
    ) {}

    #[OA\Post(
        path: '/api/prompt/questions',
        summary: 'Generate quiz questions using AI from a prompt',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['prompt'],
                properties: [
                    new OA\Property(property: 'prompt', type: 'string', example: 'Generate a question about The Count of Monte Cristo.'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Returns AI-generated question'),
            new OA\Response(response: 400, description: 'No prompt specified'),
            new OA\Response(response: 401, description: 'Not authenticated'),
        ]
    )]
    #[Route('/api/prompt/questions', methods: ['POST'])]
    public function prompt(
        Request $request,
        DecoderInterface $decoder,
        QuestionAiGeneratorService $questionAiGenerator,
    ): JsonResponse
    {
        $user = self::getLoggedInUser($this->security, 'You must be logged in to access AI features.');

        $reqData = $decoder->decode($request->getContent(), 'json');

        if (!isset($reqData['prompt'])) {
            throw new BadRequestHttpException('You must specify a prompt!', code: Response::HTTP_BAD_REQUEST);
        }

        $prompt = $reqData['prompt'];

        $question = $questionAiGenerator->generateQuestion($prompt, $user);

        return $this->json([
            'status' => 'success',
            'data' => [
                'question' => $question,
            ]
        ]);
    }
}
