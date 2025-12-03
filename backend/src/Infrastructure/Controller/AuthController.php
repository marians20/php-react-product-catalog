<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use App\Application\DTO\RegisterUserDTO;
use App\Application\UseCase\RegisterUserUseCase;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Authentication')]
#[Route('/api')]
class AuthController
{
    public function __construct(
        private readonly RegisterUserUseCase $registerUserUseCase
    ) {
    }

    #[OA\Post(
        path: '/register',
        summary: 'Register a new user',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password', 'name'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password123'),
                    new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                    new OA\Property(
                        property: 'roles',
                        type: 'array',
                        items: new OA\Items(type: 'string', enum: ['ROLE_USER', 'ROLE_ADMIN']),
                        example: ['ROLE_USER']
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'User registered successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'User registered successfully'),
                        new OA\Property(property: 'user', type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'email', type: 'string', example: 'user@example.com'),
                                new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                                new OA\Property(property: 'roles', type: 'array', items: new OA\Items(type: 'string'))
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Invalid input')
        ]
    )]
    #[Route('/register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!$data) {
                return new JsonResponse(
                    ['error' => 'Invalid JSON'],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $dto = RegisterUserDTO::fromArray($data);
            $user = $this->registerUserUseCase->execute($dto);

            return new JsonResponse([
                'message' => 'User registered successfully',
                'user' => [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'name' => $user->getName(),
                    'roles' => $user->getRoles()
                ]
            ], Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        } catch (\Exception $e) {
            return new JsonResponse(
                ['error' => 'Failed to register user'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[OA\Post(
        path: '/login',
        summary: 'Login and get JWT token',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password123')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Login successful',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'token', type: 'string', example: 'eyJ0eXAiOiJKV1QiLCJhbGc...')
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Invalid credentials')
        ]
    )]
    #[Route('/login', methods: ['POST'])]
    public function login(): JsonResponse
    {
        // This method is handled by lexik_jwt_authentication bundle
        // It's here for documentation purposes
        return new JsonResponse(['message' => 'Login handled by JWT bundle']);
    }
}
