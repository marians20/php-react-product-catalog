<?php

declare(strict_types=1);

namespace App\Application\DTO;

/**
 * Update Product DTO - Application Layer
 * Data Transfer Object for updating products
 */
class UpdateProductDTO
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $description = null,
        public readonly ?float $price = null,
        public readonly ?int $stock = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            description: $data['description'] ?? null,
            price: isset($data['price']) ? (float)$data['price'] : null,
            stock: isset($data['stock']) ? (int)$data['stock'] : null
        );
    }
}
