<?php

namespace App\Dto;

class ChatHistoryItemDto
{
    public function __construct(
        private string $userIdentifier,
        private string $response,
    ) {}

    public function getUserIdentifier(): string
    {
        return $this->userIdentifier;
    }

    public function setUserIdentifier(string $userIdentifier): void
    {
        $this->userIdentifier = $userIdentifier;
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
