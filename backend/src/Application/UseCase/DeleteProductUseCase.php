<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Repository\ProductRepositoryInterface;

/**
 * Delete Product Use Case - Application Layer
 * Orchestrates the product deletion process
 */
class DeleteProductUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository
    ) {
    }

    public function execute(int $id): bool
    {
        $product = $this->productRepository->findById($id);

        if (!$product) {
            return false;
        }

        $this->productRepository->delete($product);

        return true;
    }
}
