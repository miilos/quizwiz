<?php

namespace App\Controller;

use App\Dto\UserDto;
use App\Entity\User;
use App\Exception\AuthException;
use App\Service\Auth\UserSignupService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class AuthController extends AbstractController
{
    #[Route('/api/signup', name: 'signup', methods: ['POST'])]
    public function signUp(
        #[MapRequestPayload] UserDto $user,
        UserSignupService $userSignupService
    ): JsonResponse
    {
        $user = $userSignupService->signUp($user);

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
        UserSignupService $userSignup
    ): JsonResponse
    {
        if (!$user) {
            throw new AuthException('Invalid account activation token.');
        }

        $userSignup->activateAccount($user);

        return $this->json([
            'status' => 'success',
            'message' => 'Account activated!'
        ]);
    }
}
