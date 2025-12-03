<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\Entity\Product;
use App\Domain\Repository\ProductRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * Doctrine Product Repository - Infrastructure Layer
 * Implements the repository interface using Doctrine ORM
 */
class DoctrineProductRepository implements ProductRepositoryInterface
{
    private EntityRepository $repository;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Product::class);
    }

    public function save(Product $product): void
    {
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }

    public function findById(int $id): ?Product
    {
        return $this->repository->find($id);
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    public function delete(Product $product): void
    {
        $this->entityManager->remove($product);
        $this->entityManager->flush();
    }
}
