<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepositoryInterface;

class GetUserUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {
    }

    public function execute(int $id): ?User
    {
        return $this->userRepository->find($id);
    }
}
