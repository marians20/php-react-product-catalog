<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DTO\CreateProductDTO;
use App\Domain\Entity\Product;
use App\Domain\Repository\ProductRepositoryInterface;

/**
 * Create Product Use Case - Application Layer
 * Orchestrates the product creation process
 */
class CreateProductUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository
    ) {
    }

    public function execute(CreateProductDTO $dto): Product
    {
        $product = new Product(
            name: $dto->name,
            description: $dto->description,
            price: $dto->price,
            stock: $dto->stock
        );

        $this->productRepository->save($product);

        return $product;
    }
}
