<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use App\Application\DTO\UpdateUserDTO;
use App\Application\UseCase\GetAllUsersUseCase;
use App\Application\UseCase\GetUserUseCase;
use App\Application\UseCase\UpdateUserUseCase;
use App\Application\UseCase\DeleteUserUseCase;
use App\Application\UseCase\ToggleUserStatusUseCase;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag(name: 'Users')]
#[Route('/api/users')]
#[IsGranted('ROLE_ADMIN')]
class UserController
{
    public function __construct(
        private readonly GetAllUsersUseCase $getAllUsersUseCase,
        private readonly GetUserUseCase $getUserUseCase,
        private readonly UpdateUserUseCase $updateUserUseCase,
        private readonly DeleteUserUseCase $deleteUserUseCase,
        private readonly ToggleUserStatusUseCase $toggleUserStatusUseCase
    ) {
    }

    #[OA\Get(
        path: '/api/users',
        summary: 'Get all users',
        security: [['bearerAuth' => []]],
        tags: ['Users'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of users',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'email', type: 'string', example: 'user@example.com'),
                            new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                            new OA\Property(property: 'roles', type: 'array', items: new OA\Items(type: 'string')),
                            new OA\Property(property: 'enabled', type: 'boolean', example: true),
                            new OA\Property(property: 'createdAt', type: 'string', example: '2025-12-03 10:00:00')
                        ]
                    )
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden - Admin only')
        ]
    )]
    #[Route('', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        $users = $this->getAllUsersUseCase->execute();
        
        return new JsonResponse(
            array_map(fn($user) => $user->toArray(), $users)
        );
    }

    #[OA\Get(
        path: '/api/users/{id}',
        summary: 'Get user by ID',
        security: [['bearerAuth' => []]],
        tags: ['Users'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'User details',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'email', type: 'string', example: 'user@example.com'),
                        new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                        new OA\Property(property: 'roles', type: 'array', items: new OA\Items(type: 'string')),
                        new OA\Property(property: 'enabled', type: 'boolean', example: true),
                        new OA\Property(property: 'createdAt', type: 'string', example: '2025-12-03 10:00:00')
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'User not found')
        ]
    )]
    #[Route('/{id}', methods: ['GET'])]
    public function get(int $id): JsonResponse
    {
        $user = $this->getUserUseCase->execute($id);

        if (!$user) {
            return new JsonResponse(
                ['error' => 'User not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        return new JsonResponse($user->toArray());
    }

    #[OA\Put(
        path: '/api/users/{id}',
        summary: 'Update user',
        security: [['bearerAuth' => []]],
        tags: ['Users'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                    new OA\Property(property: 'email', type: 'string', example: 'user@example.com'),
                    new OA\Property(property: 'password', type: 'string', example: 'newpassword'),
                    new OA\Property(
                        property: 'roles',
                        type: 'array',
                        items: new OA\Items(type: 'string', enum: ['ROLE_USER', 'ROLE_ADMIN'])
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'User updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer'),
                        new OA\Property(property: 'email', type: 'string'),
                        new OA\Property(property: 'name', type: 'string'),
                        new OA\Property(property: 'roles', type: 'array', items: new OA\Items(type: 'string')),
                        new OA\Property(property: 'enabled', type: 'boolean')
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'User not found'),
            new OA\Response(response: 400, description: 'Invalid input')
        ]
    )]
    #[Route('/{id}', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!$data) {
                return new JsonResponse(
                    ['error' => 'Invalid JSON'],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $dto = UpdateUserDTO::fromArray($data);
            $user = $this->updateUserUseCase->execute($id, $dto);

            if (!$user) {
                return new JsonResponse(
                    ['error' => 'User not found'],
                    Response::HTTP_NOT_FOUND
                );
            }

            return new JsonResponse($user->toArray());
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        } catch (\Exception $e) {
            return new JsonResponse(
                ['error' => 'Failed to update user'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[OA\Delete(
        path: '/api/users/{id}',
        summary: 'Delete user',
        security: [['bearerAuth' => []]],
        tags: ['Users'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: 204, description: 'User deleted successfully'),
            new OA\Response(response: 404, description: 'User not found')
        ]
    )]
    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $result = $this->deleteUserUseCase->execute($id);

        if (!$result) {
            return new JsonResponse(
                ['error' => 'User not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[OA\Patch(
        path: '/api/users/{id}/toggle-status',
        summary: 'Enable or disable user',
        security: [['bearerAuth' => []]],
        tags: ['Users'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['enabled'],
                properties: [
                    new OA\Property(property: 'enabled', type: 'boolean', example: true)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'User status updated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer'),
                        new OA\Property(property: 'enabled', type: 'boolean'),
                        new OA\Property(property: 'message', type: 'string')
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'User not found')
        ]
    )]
    #[Route('/{id}/toggle-status', methods: ['PATCH'])]
    public function toggleStatus(int $id, Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!isset($data['enabled']) || !is_bool($data['enabled'])) {
                return new JsonResponse(
                    ['error' => 'Field "enabled" is required and must be a boolean'],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $user = $this->toggleUserStatusUseCase->execute($id, $data['enabled']);

            if (!$user) {
                return new JsonResponse(
                    ['error' => 'User not found'],
                    Response::HTTP_NOT_FOUND
                );
            }

            return new JsonResponse([
                'id' => $user->getId(),
                'enabled' => $user->isEnabled(),
                'message' => $user->isEnabled() ? 'User enabled successfully' : 'User disabled successfully'
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(
                ['error' => 'Failed to update user status'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
