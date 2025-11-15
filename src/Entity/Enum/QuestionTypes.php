<?php

namespace App\Entity\Enum;

enum QuestionTypes: string
{
    case ONE_ANSWER = 'one';
    case MULTIPLE_ANSWERS = 'multiple';
}
