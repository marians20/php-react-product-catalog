<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Repository\UserRepositoryInterface;

class GetAllUsersUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {
    }

    public function execute(): array
    {
        return $this->userRepository->findAll();
    }
}
