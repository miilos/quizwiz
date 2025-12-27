<?php

namespace App\GraphQL\Loader;

use App\Dto\UserDto;
use App\Repository\UserRepository;

class UserLoader
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {}

    public function all(array $userIds): array
    {
        $users = $this->userRepository->findBy(['id' => $userIds]);

        $usersMapped = [];
        foreach ($users as $user) {
            $usersMapped[$user->getId()] = $user;
        }

        return array_map(
            static fn($userId) => $usersMapped[$userId] ?? null,
            $userIds
        );
    }
}
