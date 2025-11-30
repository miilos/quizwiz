<?php

namespace App\Repository;

use App\Dto\ChatHistoryItemDto;
use App\Entity\ChatHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ChatHistory>
 */
class ChatHistoryRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, ChatHistory::class);
        $this->entityManager = $entityManager;
    }

    public function persistResponse(ChatHistoryItemDto $chatHistoryItemDto): ChatHistory
    {
        $chatHistoryItem = new ChatHistory();
        $chatHistoryItem->setUserId($chatHistoryItemDto->getUserIdentifier());
        $chatHistoryItem->setResponse($chatHistoryItemDto->getResponse());

        $this->entityManager->persist($chatHistoryItem);
        $this->entityManager->flush();
        return $chatHistoryItem;
    }

    public function getChatHistoryItems(string $userIdentifier, int $itemCount): array
    {
        return $this->createQueryBuilder('chat_history')
            ->andWhere('chat_history.userId = :userId')
            ->setParameter('userId', $userIdentifier)
            ->setMaxResults($itemCount)
            ->getQuery()
            ->getResult();
    }
}
