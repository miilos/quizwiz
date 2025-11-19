<?php

namespace App\Entity\Enum;

enum QuestionTypes: string
{
    case ONE_ANSWER = 'one';
    case MULTIPLE_ANSWERS = 'multiple';

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
