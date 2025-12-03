<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use App\Application\DTO\CreateProductDTO;
use App\Application\DTO\UpdateProductDTO;
use App\Application\UseCase\CreateProductUseCase;
use App\Application\UseCase\DeleteProductUseCase;
use App\Application\UseCase\GetAllProductsUseCase;
use App\Application\UseCase\GetProductUseCase;
use App\Application\UseCase\UpdateProductUseCase;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Product Controller - Infrastructure Layer
 * Handles HTTP requests and responses for product operations
 */
#[OA\Tag(name: 'Products')]
#[Route('/api/products')]
class ProductController
{
    public function __construct(
        private readonly CreateProductUseCase $createProductUseCase,
        private readonly UpdateProductUseCase $updateProductUseCase,
        private readonly DeleteProductUseCase $deleteProductUseCase,
        private readonly GetAllProductsUseCase $getAllProductsUseCase,
        private readonly GetProductUseCase $getProductUseCase
    ) {
    }

    #[OA\Get(
        path: '/api/products',
        summary: 'Get all products',
        tags: ['Products'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful operation',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/Product')
                )
            )
        ]
    )]
    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse
    {
        try {
            $products = $this->getAllProductsUseCase->execute();
            
            return new JsonResponse(
                array_map(fn($product) => $product->toArray(), $products)
            );
        } catch (\Exception $e) {
            return new JsonResponse(
                ['error' => 'Failed to retrieve products'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[OA\Get(
        path: '/api/products/{id}',
        summary: 'Get a product by ID',
        tags: ['Products'],
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
                description: 'Successful operation',
                content: new OA\JsonContent(ref: '#/components/schemas/Product')
            ),
            new OA\Response(response: 404, description: 'Product not found')
        ]
    )]
    #[Route('/{id}', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id): JsonResponse
    {
        try {
            $product = $this->getProductUseCase->execute($id);

            if (!$product) {
                return new JsonResponse(
                    ['error' => 'Product not found'],
                    Response::HTTP_NOT_FOUND
                );
            }

            return new JsonResponse($product->toArray());
        } catch (\Exception $e) {
            return new JsonResponse(
                ['error' => 'Failed to retrieve product'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[OA\Post(
        path: '/api/products',
        summary: 'Create a new product',
        tags: ['Products'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'description', 'price', 'stock'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Laptop'),
                    new OA\Property(property: 'description', type: 'string', example: 'High-performance laptop'),
                    new OA\Property(property: 'price', type: 'number', format: 'float', example: 999.99),
                    new OA\Property(property: 'stock', type: 'integer', example: 50)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Product created',
                content: new OA\JsonContent(ref: '#/components/schemas/Product')
            ),
            new OA\Response(response: 400, description: 'Invalid input')
        ]
    )]
    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!$data) {
                return new JsonResponse(
                    ['error' => 'Invalid JSON'],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $dto = CreateProductDTO::fromArray($data);
            $product = $this->createProductUseCase->execute($dto);

            return new JsonResponse(
                $product->toArray(),
                Response::HTTP_CREATED
            );
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        } catch (\Exception $e) {
            return new JsonResponse(
                ['error' => 'Failed to create product'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[OA\Put(
        path: '/api/products/{id}',
        summary: 'Update a product',
        tags: ['Products'],
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
                    new OA\Property(property: 'name', type: 'string', example: 'Updated Laptop'),
                    new OA\Property(property: 'description', type: 'string', example: 'Updated description'),
                    new OA\Property(property: 'price', type: 'number', format: 'float', example: 899.99),
                    new OA\Property(property: 'stock', type: 'integer', example: 30)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Product updated',
                content: new OA\JsonContent(ref: '#/components/schemas/Product')
            ),
            new OA\Response(response: 404, description: 'Product not found'),
            new OA\Response(response: 400, description: 'Invalid input')
        ]
    )]
    #[Route('/{id}', methods: ['PUT'], requirements: ['id' => '\d+'])]
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

            $dto = UpdateProductDTO::fromArray($data);
            $product = $this->updateProductUseCase->execute($id, $dto);

            if (!$product) {
                return new JsonResponse(
                    ['error' => 'Product not found'],
                    Response::HTTP_NOT_FOUND
                );
            }

            return new JsonResponse($product->toArray());
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        } catch (\Exception $e) {
            return new JsonResponse(
                ['error' => 'Failed to update product'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[OA\Delete(
        path: '/api/products/{id}',
        summary: 'Delete a product',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: 204, description: 'Product deleted'),
            new OA\Response(response: 404, description: 'Product not found')
        ]
    )]
    #[Route('/{id}', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $deleted = $this->deleteProductUseCase->execute($id);

            if (!$deleted) {
                return new JsonResponse(
                    ['error' => 'Product not found'],
                    Response::HTTP_NOT_FOUND
                );
            }

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return new JsonResponse(
                ['error' => 'Failed to delete product'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
