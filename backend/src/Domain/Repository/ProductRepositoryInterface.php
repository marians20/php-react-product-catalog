<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Product;

/**
 * Product Repository Interface - Domain Layer
 * Defines the contract for product persistence without implementation details
 */
interface ProductRepositoryInterface
{
    public function save(Product $product): void;
    
    public function findById(int $id): ?Product;
    
    public function findAll(): array;
    
    public function delete(Product $product): void;
}
