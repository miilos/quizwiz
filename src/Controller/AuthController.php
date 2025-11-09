<?php

namespace App\Controller;

use App\Dto\UserDto;
use App\Entity\User;
use App\Exception\AuthException;
use App\Service\Auth\UserManagerService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

class AuthController extends AbstractController
{
    public function __construct(
        private UserManagerService $userSignupService
    ) {}

    #[Route('/api/signup', name: 'signup', methods: ['POST'])]
    public function signUp(
        #[MapRequestPayload] UserDto $user,
    ): JsonResponse
    {
        $user = $this->userSignupService->signUp($user);

        return $this->json([
            'status' => 'success',
            'data' => [
                'user' => $user,
            ]
        ], 201, context: [ 'groups' => 'basicUserInfo' ]);
    }

    #[Route('/api/login', name: 'login', methods: ['POST'])]
    public function logIn(
        #[CurrentUser] ?User $user,
    ): JsonResponse
    {
        if (null === $user) {
            return $this->json([
                'status' => 'error',
                'message' => 'Incorrect email or password.'
            ]);
        }

        return $this->json([
            'status' => 'success',
            'message' => 'Login successful!',
        ]);
    }

    // TODO: put a RedirectResponse with an actual route name or url here once the frontend exists
    #[Route('/api/account/activate/{token}', name: 'activate_account')]
    public function activateAccount(
        #[MapEntity(mapping: ['token' => 'accountActivationToken'])] ?User $user,
    ): JsonResponse
    {
        if (!$user) {
            throw new AuthException('Invalid account activation token.');
        }

        $this->userSignupService->activateAccount($user);

        return $this->json([
            'status' => 'success',
            'message' => 'Account activated!'
        ]);
    }

    #[Route('/api/account/password/forgot', name: 'forgot_password')]
    public function forgotPassword(
        #[CurrentUser] ?User $user,
    ): JsonResponse
    {
        if (null === $user) {
            throw new AuthException('You need to log in first.');
        }

        $this->userSignupService->createPasswordResetToken($user);

        return $this->json([
            'status' => 'success',
            'message' => 'Password reset token sent to your email!',
        ]);
    }

    #[Route('/api/account/password/reset', name: 'reset_password', methods: ['POST'])]
    public function resetPassword(
        Request $request,
        DecoderInterface $decoder,
    ): JsonResponse
    {
        $reqData = $decoder->decode($request->getContent(), 'json');
        $token = $reqData['token'];
        $newPassword = $reqData['newPassword'];

        if (!$token || !$newPassword) {
            throw new AuthException('Token and password must be sent.');
        }

        $this->userSignupService->resetPassword($token, $newPassword);

        return $this->json([
            'status' => 'success',
            'message' => 'Password reset successful!',
        ]);
    }
}
