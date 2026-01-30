<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    #[OA\Get(
        path: "/products",
        summary: "Listar productos",
        description: "Retorna lista paginada de productos",
        tags: ["Productos"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "per_page",
                in: "query",
                required: false,
                description: "Elementos por página (máx 100)",
                schema: new OA\Schema(type: "integer", default: 10)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Lista de productos",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "data", type: "array", items: new OA\Items(type: "object")),
                        new OA\Property(property: "pagination", type: "object")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "No autenticado")
        ]
    )]
    public function index(): JsonResponse
    {
        $perPage = min((int) request()->get('per_page', 10), 100);
        
        $products = Product::with('currency')
            ->orderByDesc('id')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $products->items(),
            'pagination' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ]
        ]);
    }

    #[OA\Post(
        path: "/products",
        summary: "Crear producto",
        description: "Crea un nuevo producto",
        tags: ["Productos"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "price", "currency_id"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Producto ejemplo"),
                    new OA\Property(property: "description", type: "string", example: "Descripción del producto"),
                    new OA\Property(property: "price", type: "number", format: "decimal", example: 99.99),
                    new OA\Property(property: "currency_id", type: "integer", example: 1)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Producto creado"),
            new OA\Response(response: 401, description: "No autenticado"),
            new OA\Response(response: 422, description: "Errores de validación")
        ]
    )]
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Producto creado exitosamente',
            'data' => $product->load('currency')
        ], 201);
    }

    #[OA\Get(
        path: "/products/{id}",
        summary: "Ver producto",
        description: "Retorna un producto específico",
        tags: ["Productos"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID del producto",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Datos del producto"),
            new OA\Response(response: 401, description: "No autenticado"),
            new OA\Response(response: 404, description: "Producto no encontrado")
        ]
    )]
    public function show(Product $product): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $product->load('currency')
        ]);
    }

    #[OA\Put(
        path: "/products/{id}",
        summary: "Actualizar producto",
        description: "Actualiza un producto existente",
        tags: ["Productos"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID del producto",
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string"),
                    new OA\Property(property: "description", type: "string"),
                    new OA\Property(property: "price", type: "number", format: "decimal"),
                    new OA\Property(property: "currency_id", type: "integer")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Producto actualizado"),
            new OA\Response(response: 401, description: "No autenticado"),
            new OA\Response(response: 404, description: "Producto no encontrado"),
            new OA\Response(response: 422, description: "Errores de validación")
        ]
    )]
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Producto actualizado exitosamente',
            'data' => $product->fresh()->load('currency')
        ]);
    }

    #[OA\Delete(
        path: "/products/{id}",
        summary: "Eliminar producto",
        description: "Elimina un producto",
        tags: ["Productos"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID del producto",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Producto eliminado"),
            new OA\Response(response: 401, description: "No autenticado"),
            new OA\Response(response: 404, description: "Producto no encontrado")
        ]
    )]
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado exitosamente'
        ]);
    }
}
