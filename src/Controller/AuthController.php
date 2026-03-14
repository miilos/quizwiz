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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

class AuthController extends AbstractController
{
    public function __construct(
        private readonly UserManagerService $userManagerService,
        private readonly UserRepository $userRepository,
        private readonly TokenService $tokenService,
    ) {}

    #[Route('/api/signup', name: 'signup', methods: ['POST'])]
    public function signUp(
        #[MapRequestPayload] UserDto $user,
    ): JsonResponse
    {
        $user = $this->userManagerService->signUp($user);
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

    #[Route('/api/account/edit', name: 'edit', methods: ['POST'])]
    public function editProfile(
        #[CurrentUser] ?User $user,
        Request $request,
    ): JsonResponse
    {
        if (!$user) {
            throw new AuthException('You must log in first!', 401);
        }

        $data = $request->toArray();
        $user = $this->userManagerService->editAccount($user, $data);

        return $this->json([
            'status' => 'success',
            'data' => [
                'user' => $user,
            ]
        ], context: ['groups' => 'basicUserInfo']);
    }

    #[Route('/api/admin/account/{id}/edit', name: 'edit_admin', methods: ['POST'])]
    public function editProfileAdmin(
        int $id,
        Request $request,
    ): JsonResponse
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);

        if (!$user) {
            throw new AuthException('You must log in first!', 401);
        }

        $data = $request->toArray();
        $user = $this->userManagerService->editAccount($user, $data);

        return $this->json([
            'status' => 'success',
            'data' => [
                'user' => $user,
            ]
        ], context: ['groups' => 'basicUserInfo']);
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

        $this->userManagerService->activateAccount($user);

        return $this->json([
            'status' => 'success',
            'message' => 'Account activated!'
        ]);
    }

    #[Route('/api/account/password/forgot', name: 'forgot_password', methods: ['POST'])]
    public function forgotPassword(
        Request $request,
    ): JsonResponse
    {
        $email = $request->toArray()['email'] ?? null;

        if (!$email) {
            throw new BadRequestHttpException('No email specified');
        }

        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            throw new BadRequestHttpException('Invalid email address');
        }

        $this->userManagerService->createPasswordResetToken($user);

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

        $this->userManagerService->resetPassword($token, $newPassword);

        return $this->json([
            'status' => 'success',
            'message' => 'Password reset successful!',
        ]);
    }

    #[Route('/api/account/{id}/delete', name: 'delete_account', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteAccount(
        int $id
    ): JsonResponse
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);

        if (!$user) {
            throw new BadRequestHttpException('User not found!');
        }

        $this->userManagerService->deleteUser($user);

        return $this->json([], Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/account/list', name: 'accounts_list', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function getAllAccounts(): JsonResponse
    {
        $users = $this->userRepository->findAll();

        return $this->json([
            'status' => 'success',
            'data' => [
                'users' => $users,
            ]
        ], context: ['groups' => 'fullUserInfo']);
    }
}
