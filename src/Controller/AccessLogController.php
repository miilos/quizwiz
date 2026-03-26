<?php

namespace App\Controller;

use App\Repository\AccessLogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AccessLogController extends AbstractController
{
    public function __construct(
        private readonly AccessLogRepository $accessLogRepository
    ) {}

    #[Route('/api/access-log', name: 'access_log', methods: ['GET'])]
    #[IsGranted("ROLE_ADMIN")]
    public function getAllLogs(): JsonResponse
    {
        $logs = $this->accessLogRepository->findAll();

        return $this->json([
            'status' => 'success',
            'data' => [
                'logs' => $logs
            ]
        ], context: ['groups' => ['access-log']]);
    }
}
