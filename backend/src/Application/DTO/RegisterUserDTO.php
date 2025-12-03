<?php

declare(strict_types=1);

namespace App\Application\DTO;

class RegisterUserDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly string $name,
        public readonly array $roles = []
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            email: $data['email'] ?? '',
            password: $data['password'] ?? '',
            name: $data['name'] ?? '',
            roles: $data['roles'] ?? []
        );
    }
}
