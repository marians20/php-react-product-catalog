<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DTO\UpdateProductDTO;
use App\Domain\Entity\Product;
use App\Domain\Repository\ProductRepositoryInterface;

/**
 * Update Product Use Case - Application Layer
 * Orchestrates the product update process
 */
class UpdateProductUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository
    ) {
    }

    public function execute(int $id, UpdateProductDTO $dto): ?Product
    {
        $product = $this->productRepository->findById($id);

        if (!$product) {
            return null;
        }

        if ($dto->name !== null) {
            $product->setName($dto->name);
        }

        if ($dto->description !== null) {
            $product->setDescription($dto->description);
        }

        if ($dto->price !== null) {
            $product->setPrice($dto->price);
        }

        if ($dto->stock !== null) {
            $product->setStock($dto->stock);
        }

        $this->productRepository->save($product);

        return $product;
    }
}
