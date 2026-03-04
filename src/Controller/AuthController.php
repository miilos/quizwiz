<?php

namespace App\Controller;

use App\Dto\UserDto;
use App\Entity\User;
use App\Exception\AuthException;
use App\Repository\UserRepository;
use App\Service\Auth\TokenService;
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
        private readonly UserManagerService $userSignupService,
        private readonly UserRepository $userRepository,
        private readonly TokenService $tokenService,
    ) {}

    #[Route('/api/signup', name: 'signup', methods: ['POST'])]
    public function signUp(
        #[MapRequestPayload] UserDto $user,
    ): JsonResponse
    {
        $user = $this->userSignupService->signUp($user);
        $token = $this->tokenService->generateToken($user);

        return $this->json([
            'status' => 'success',
            'data' => [
                'user' => $user,
                'token' => $token->getToken(),
            ]
        ], 201, context: [ 'groups' => 'basicUserInfo' ]);
    }

    #[Route('/api/login', name: 'login', methods: ['POST'])]
    public function logIn(
        Request $request,
    ): JsonResponse
    {
        $email = $request->toArray()['email'] ?? null;
        $password = $request->toArray()['password'] ?? null;

        if (!$email || !$password) {
            throw new AuthException('Incorrect email or password!', 401);
        }

        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            throw new AuthException('Incorrect email or password!', 401);
        }

        if (!password_verify($password, $user->getPassword())) {
            throw new AuthException('Incorrect email or password!', 401);
        }

        $token = $this->tokenService->generateToken($user);

        return $this->json([
            'status' => 'success',
            'data' => [
                'user' => $user,
                'token' => $token->getToken(),
            ]
        ], context: ['groups' => 'basicUserInfo']);
    }

    #[Route('/api/me', name: 'me', methods: ['GET'])]
    public function me(
        #[CurrentUser] ?User $user
    ): JsonResponse
    {
        if (null === $user) {
            throw new AuthException('User not found!', 401);
        }

        return $this->json([
            'status' => 'success',
            'data' => [
                'user' => $user,
            ]
        ], context: ['groups' => 'fullUserInfo']);
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
