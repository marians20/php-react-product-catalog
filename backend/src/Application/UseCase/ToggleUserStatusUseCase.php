<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepositoryInterface;

class ToggleUserStatusUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {
    }

    public function execute(int $id, bool $enabled): ?User
    {
        $user = $this->userRepository->find($id);
        
        if (!$user) {
            return null;
        }

        $user->setEnabled($enabled);
        $this->userRepository->save($user);

        return $user;
    }
}
