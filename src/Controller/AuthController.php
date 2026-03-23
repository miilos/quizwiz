<?php

namespace App\Controller;

use App\Dto\UserDto;
use App\Entity\User;
use App\Exception\AuthException;
use App\Repository\UserRepository;
use App\Service\Auth\TokenService;
use App\Service\Auth\UserManagerService;
use App\Service\Util\IPLoggerTrait;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
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

#[OA\Tag(name: 'Auth')]
class AuthController extends AbstractController
{
    use IPLoggerTrait;

    public function __construct(
        private readonly UserManagerService $userManagerService,
        private readonly UserRepository $userRepository,
        private readonly TokenService $tokenService,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    #[OA\Post(
        path: '/api/signup',
        summary: 'Register a new user',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['username', 'email', 'password'],
                properties: [
                    new OA\Property(property: 'username', type: 'string'),
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'password', type: 'string', format: 'password'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'User created, returns user and auth token'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
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

    #[OA\Post(
        path: '/api/login',
        summary: 'Authenticate a user',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'password', type: 'string', format: 'password'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Login successful, returns user and auth token'),
            new OA\Response(response: 401, description: 'Invalid credentials'),
        ]
    )]
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

        self::logAccess($request, $this->entityManager);

        return $this->json([
            'status' => 'success',
            'data' => [
                'user' => $user,
                'token' => $token->getToken(),
            ]
        ], context: ['groups' => 'basicUserInfo']);
    }

    #[OA\Get(
        path: '/api/me',
        summary: 'Get the currently authenticated user',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Returns the authenticated user'),
            new OA\Response(response: 401, description: 'Not authenticated'),
        ]
    )]
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

    #[OA\Post(
        path: '/api/account/edit',
        summary: 'Edit the authenticated user\'s profile',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'username', type: 'string'),
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'password', type: 'string', format: 'password'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Profile updated, returns updated user'),
            new OA\Response(response: 401, description: 'Not authenticated'),
        ]
    )]
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

    #[OA\Post(
        path: '/api/admin/account/{id}/edit',
        summary: 'Edit any user\'s profile (admin only)',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'username', type: 'string'),
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'password', type: 'string', format: 'password'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Profile updated, returns updated user'),
            new OA\Response(response: 401, description: 'User not found'),
        ]
    )]
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
    #[OA\Get(
        path: '/api/account/activate/{token}',
        summary: 'Activate a user account via email token',
        parameters: [
            new OA\Parameter(name: 'token', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Account activated'),
            new OA\Response(response: 401, description: 'Invalid activation token'),
        ]
    )]
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

    #[OA\Post(
        path: '/api/account/password/forgot',
        summary: 'Request a password reset email',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Reset token sent to email'),
            new OA\Response(response: 400, description: 'Missing or invalid email'),
        ]
    )]
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

    #[OA\Post(
        path: '/api/account/password/reset',
        summary: 'Reset password using a reset token',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['token', 'newPassword'],
                properties: [
                    new OA\Property(property: 'token', type: 'string'),
                    new OA\Property(property: 'newPassword', type: 'string', format: 'password'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Password reset successful'),
            new OA\Response(response: 401, description: 'Missing token or password'),
        ]
    )]
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

    #[OA\Delete(
        path: '/api/account/{id}/delete',
        summary: 'Delete a user account (admin only)',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Account deleted'),
            new OA\Response(response: 400, description: 'User not found'),
            new OA\Response(response: 403, description: 'Access denied'),
        ]
    )]
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

    #[OA\Get(
        path: '/api/account/list',
        summary: 'List all user accounts (admin only)',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Returns list of all users'),
            new OA\Response(response: 403, description: 'Access denied'),
        ]
    )]
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
