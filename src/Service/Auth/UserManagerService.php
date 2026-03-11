<?php

namespace App\Service\Auth;

use App\Dto\UserDto;
use App\Entity\User;
use App\Exception\AuthException;
use App\Repository\UserRepository;
use App\Service\Util\MailerService;
use Doctrine\ORM\EntityManagerInterface;

class UserManagerService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly MailerService $mailer,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    public function signUp(UserDto $userDto): User
    {
        $userDto->setAccountActivationToken(
            $this->generateToken()
        );

        $user = $this->userRepository->createUser($userDto);
        $userDto->setPassword('');

        $this->sendAccountActivationEmail($userDto);

        return $user;
    }

    public function editAccount(User $user, array $data): User
    {
        foreach ($data as $key => $value) {
            $setterFn = 'set' . ucfirst($key);

            if (method_exists($user, $setterFn)) {
                $user->$setterFn($value);
            }
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    private function generateToken(): string
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

    public function createPasswordResetToken(User $user): string
    {
        $resetToken = $this->generateToken();

        $this->userRepository->setPasswordResetToken($user, $resetToken);
        $this->mailer->sendPasswordResetToken(
            $user->getEmail(),
            'Password Reset Token (valid for 15 minutes)',
            $resetToken
        );

        return $resetToken;
    }

    public function resetPassword(string $token, string $newPassword): User
    {
        $user = $this->userRepository->findOneBy(['passwordResetToken' => $token]);

        if (!$user) {
            throw new AuthException('Invalid password reset token.');
        }

        $now = new \DateTime('now');
        if ($user->getPasswordResetExpires() < $now) {
            throw new AuthException('Password reset expired.');
        }

        $this->userRepository->resetPassword($user, $newPassword);

        return $user;
    }
}
