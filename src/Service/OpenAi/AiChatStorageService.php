<?php

namespace App\Service\OpenAi;

use App\Dto\ChatHistoryItemDto;
use App\Entity\ChatHistory;
use App\Entity\User;
use App\Repository\ChatHistoryRepository;
use Symfony\Component\Security\Core\User\UserInterface;

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

    public function getChatHistory(User $user, int $itemCount = 10): array
    {
        return $this->chatHistoryRepository->getChatHistoryItems($user, $itemCount);
    }
}
