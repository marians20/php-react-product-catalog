<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

class DoctrineUserRepository implements UserRepositoryInterface, PasswordUpgraderInterface
{
    private EntityRepository $repository;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(User::class);
    }

    public function save(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function find(int $id): ?User
    {
        return $this->repository->find($id);
    }

    public function findById(int $id): ?User
    {
        return $this->repository->find($id);
    }

    public function findOneByEmail(string $email): ?User
    {
        return $this->repository->findOneBy(['email' => $email]);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->repository->findOneBy(['email' => $email]);
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    public function delete(User $user): void
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new \InvalidArgumentException('User must be an instance of ' . User::class);
        }

        $user->setPassword($newHashedPassword);
        $this->save($user);
    }
}
