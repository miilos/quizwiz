<?php

namespace App\Service\Auth;

use App\Entity\Quiz;
use App\Entity\User;
use GraphQL\Error\UserError;

class AuthorizationService
{
    public function authorizeQuizActions(User $user, Quiz $quiz): void
    {
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return;
        }

        if ($user->getId() !== $quiz->getAuthor()->getId()) {
            throw new UserError('You are not authorized to edit this quiz');
        }
    }
}
