<?php

namespace App\Repository;

use App\Dto\UserDto;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        ManagerRegistry $registry,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
    )
    {
        parent::__construct($registry, User::class);
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function createUser(UserDto $userDto): User
    {
        $user = new User();
        $user->setUsername($userDto->getUsername());
        $user->setEmail($userDto->getEmail());
        $user->setPassword(
            $this->passwordHasher->hashPassword(
                $user,
                $userDto->getPassword()
            )
        );
        $user->setRoles($userDto->getRoles());
        $user->setAccountActivationToken($userDto->getAccountActivationToken());

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $user;
    }

    public function activateAccount(User $user): User
    {
        $user->setAccountActivationToken(null);
        $user->setIsActivated(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $user;
    }

    public function setPasswordResetToken(User $user, string $resetToken): User
    {
        $user->setPasswordResetToken($resetToken);
        $user->setPasswordResetExpires(new \DateTime('+15 minutes'));

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $user;
    }

    public function resetPassword(User $user, string $newPassword): User
    {
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $newPassword)
        );
        $user->setPasswordResetToken(null);
        $user->setPasswordResetExpires(null);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $user;
    }
}
