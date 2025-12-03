<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Entity\Product;
use App\Domain\Repository\ProductRepositoryInterface;

/**
 * Get Product Use Case - Application Layer
 * Retrieves a single product by ID
 */
class GetProductUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository
    ) {
    }

    public function execute(int $id): ?Product
    {
        return $this->productRepository->findById($id);
    }
}
