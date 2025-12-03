<?php

declare(strict_types=1);

namespace App\Application\DTO;

/**
 * Create Product DTO - Application Layer
 * Data Transfer Object for creating products
 */
class CreateProductDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly float $price,
        public readonly int $stock
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? '',
            description: $data['description'] ?? '',
            price: (float)($data['price'] ?? 0),
            stock: (int)($data['stock'] ?? 0)
        );
    }
}
