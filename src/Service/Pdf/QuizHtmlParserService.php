<?php

namespace App\Service\Pdf;

use App\Entity\Quiz;
use DateTime;
use Twig\Environment;

class QuizHtmlParserService
{
    private const QUIZ_PDF_TEMPLATE_PATH = 'pdf/quiz_pdf.html.twig';

    public function __construct(
        private readonly Environment $twig,
    ) {}

    public function toHtml(Quiz $quiz): string
    {
        return $this->twig->render(self::QUIZ_PDF_TEMPLATE_PATH, [
            'quiz' => $quiz,
            'date' => (new DateTime())->format('d.m.Y.'),
        ]);
    }

    public function formatFileName(string $fileName): string
    {
        $nameParts = array_map(
            static fn($word) => strtolower($word),
            explode(' ', $fileName)
        );
        return 'quizwiz-'.implode('-', $nameParts).'-quiz';
    }
}
