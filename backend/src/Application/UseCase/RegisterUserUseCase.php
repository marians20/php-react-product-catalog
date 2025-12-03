<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DTO\RegisterUserDTO;
use App\Domain\Entity\User;
use App\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterUserUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function execute(RegisterUserDTO $dto): User
    {
        // Check if user already exists
        $existingUser = $this->userRepository->findByEmail($dto->email);
        if ($existingUser) {
            throw new \InvalidArgumentException('User with this email already exists');
        }

        // Create new user
        $user = new User($dto->email, $dto->name, $dto->roles);
        
        // Hash password
        $hashedPassword = $this->passwordHasher->hashPassword($user, $dto->password);
        $user->setPassword($hashedPassword);

        // Save user
        $this->userRepository->save($user);

        return $user;
    }
}
