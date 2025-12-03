<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DTO\UpdateUserDTO;
use App\Domain\Entity\User;
use App\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UpdateUserUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function execute(int $id, UpdateUserDTO $dto): ?User
    {
        $user = $this->userRepository->find($id);
        
        if (!$user) {
            return null;
        }

        if ($dto->name !== null) {
            $user->setName($dto->name);
        }

        if ($dto->email !== null) {
            // Check if email is already taken by another user
            $existingUser = $this->userRepository->findOneByEmail($dto->email);
            if ($existingUser && $existingUser->getId() !== $id) {
                throw new \InvalidArgumentException('User with this email already exists');
            }
            $user->setEmail($dto->email);
        }

        if ($dto->password !== null) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $dto->password);
            $user->setPassword($hashedPassword);
        }

        if ($dto->roles !== null) {
            $user->setRoles($dto->roles);
        }

        $this->userRepository->save($user);

        return $user;
    }
}
