<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Repository\UserRepositoryInterface;

class DeleteUserUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {
    }

    public function execute(int $id): bool
    {
        $user = $this->userRepository->find($id);
        
        if (!$user) {
            return false;
        }

        $this->userRepository->delete($user);
        return true;
    }
}
