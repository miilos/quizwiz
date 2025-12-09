<?php

namespace App\Dto;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

class ChatHistoryItemDto
{
    public function __construct(
        private User $user,
        private string $response,
    ) {}

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getResponse(): string
    {
        return $this->response;
    }

    public function setResponse(string $response): void
    {
        $this->response = $response;
    }
}
