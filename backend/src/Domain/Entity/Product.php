<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;

/**
 * Product Entity - Domain Layer
 * Pure domain object with business logic
 */
#[OA\Schema(
    schema: 'Product',
    title: 'Product',
    description: 'Product entity',
    required: ['name', 'description', 'price', 'stock'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Laptop'),
        new OA\Property(property: 'description', type: 'string', example: 'High-performance laptop'),
        new OA\Property(property: 'price', type: 'number', format: 'float', example: 999.99),
        new OA\Property(property: 'stock', type: 'integer', example: 50),
        new OA\Property(property: 'createdAt', type: 'string', format: 'date-time', example: '2025-12-03 12:00:00'),
        new OA\Property(property: 'updatedAt', type: 'string', format: 'date-time', example: '2025-12-03 12:00:00')
    ]
)]
#[ORM\Entity]
#[ORM\Table(name: 'products')]
#[ORM\HasLifecycleCallbacks]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\Column(type: 'float')]
    private float $price;

    #[ORM\Column(type: 'integer')]
    private int $stock;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        string $name,
        string $description,
        float $price,
        int $stock
    ) {
        $this->setName($name);
        $this->setDescription($description);
        $this->setPrice($price);
        $this->setStock($stock);
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        if (empty(trim($name))) {
            throw new \InvalidArgumentException('Product name cannot be empty');
        }
        $this->name = $name;
        $this->touch();
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
        $this->touch();
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        if ($price < 0) {
            throw new \InvalidArgumentException('Price cannot be negative');
        }
        $this->price = $price;
        $this->touch();
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function setStock(int $stock): void
    {
        if ($stock < 0) {
            throw new \InvalidArgumentException('Stock cannot be negative');
        }
        $this->stock = $stock;
        $this->touch();
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    private function touch(): void
    {
        if (isset($this->updatedAt)) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock,
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}
