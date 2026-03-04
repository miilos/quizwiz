<?php

namespace App\Twig;

use Twig\Attribute\AsTwigFilter;

class IndexToLetterExtension
{
    private const LETTERS = 'abcdefghijklmnopqrstuvwxyz';

    #[AsTwigFilter('toLetter')]
    public function toLetter(int $index): string
    {
        return self::LETTERS[$index];
    }
}
