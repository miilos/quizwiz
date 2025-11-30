<?php

namespace App\Controller;

use App\Exception\AuthException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

trait LoggedInUserAwareTrait
{
    public function getLoggedInUser(Security $security, string $message = 'You must be logged in to access this route.'): UserInterface
    {
        $user = $security->getUser();

        if (!$user) {
            throw new AuthException(
                $message,
                Response::HTTP_FORBIDDEN
            );
        }

        return $user;
    }
}
