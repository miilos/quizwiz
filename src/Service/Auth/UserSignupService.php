<?php

namespace App\Service\Auth;

use App\Dto\UserDto;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Util\MailerService;

class UserSignupService
{
    public function __construct(
        private UserRepository $userRepository,
        private MailerService $mailer
    ) {}

    public function signUp(UserDto $userDto): User
    {
        $userDto->setAccountActivationToken(
            $this->generateAccountActivationToken()
        );

        $user = $this->userRepository->createUser($userDto);
        $userDto->setPassword('');

        $this->sendAccountActivationEmail($userDto);

        return $user;
    }

    private function generateAccountActivationToken(): string
    {
        return bin2hex(random_bytes(16));
    }

    private function sendAccountActivationEmail(UserDto $userDto): void
    {
        $this->mailer->sendAccountActivationLink(
            $userDto->getEmail(),
            'QuizWiz Account Activation',
            $userDto->getAccountActivationToken()
        );
    }

    public function activateAccount(User $user): User
    {
        $this->userRepository->activateAccount($user);
        return $user;
    }
}
