<?php

namespace App\Controller;

use App\Exception\ApiResourceNotFoundException;
use App\Repository\QuizRepository;
use App\Service\Pdf\QuizHtmlParserService;
use Dompdf\Dompdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FileGenController extends AbstractController
{
    public function __construct(
        private readonly QuizRepository $quizRepository,
        private readonly QuizHtmlParserService $quizHtmlParserService,
    ) {}

    #[Route('/api/quiz/{id}/pdf', name: 'quiz-pdf', methods: ['GET'])]
    public function generateQuizPdf(
        int $id
    ): Response
    {
        $quiz = $this->quizRepository->findOneBy(['id' => $id]);

        if (!$quiz) {
            throw new ApiResourceNotFoundException("Quiz with id $id not found", 404);
        }

        $domPdf = new Dompdf();

        $fileContentHtml = $this->quizHtmlParserService->toHtml($quiz);
        $fileName = $this->quizHtmlParserService->formatFileName($quiz->getTitle());

        $domPdf->loadHtml($fileContentHtml);
        $domPdf->setPaper('A4');
        $domPdf->render();

        $pdf = $domPdf->output();

        return new Response(
            $pdf,
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$fileName.'.pdf"',
                'Content-Length' => strlen($pdf)
            ]
        );
    }
}
