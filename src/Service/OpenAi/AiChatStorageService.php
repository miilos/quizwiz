<?php

namespace App\Service\OpenAi;

use App\Dto\ChatHistoryItemDto;
use App\Entity\ChatHistory;
use App\Repository\ChatHistoryRepository;

class AiChatStorageService
{
    public function __construct(
        private ChatHistoryRepository $chatHistoryRepository,
    ) {}

    public function saveResponseToHistory(ChatHistoryItemDto $chatHistoryItemDto): void
    {
        $this->chatHistoryRepository->persistResponse($chatHistoryItemDto);
    }

    /** @param ChatHistory[] $chatHistoryItems */
    public function formatResponseHistory(array $chatHistoryItems): string
    {
        $formattedHistory = '';

        foreach ($chatHistoryItems as $chatHistoryItem) {
            $formattedHistory .= sprintf('%s\n', $chatHistoryItem->getResponse());
        }

        return $formattedHistory;
    }

    public function getChatHistory(string $userIdentifier, int $itemCount = 10): array
    {
        return $this->chatHistoryRepository->getChatHistoryItems($userIdentifier, $itemCount);
    }
}
