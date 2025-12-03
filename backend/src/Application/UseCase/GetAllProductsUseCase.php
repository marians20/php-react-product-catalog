<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Repository\ProductRepositoryInterface;

/**
 * Get All Products Use Case - Application Layer
 * Retrieves all products
 */
class GetAllProductsUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository
    ) {
    }

    public function execute(): array
    {
        return $this->productRepository->findAll();
    }
}
