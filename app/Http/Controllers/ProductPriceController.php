<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductPrice;
use App\Http\Requests\StoreProductPriceRequest;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ProductPriceController extends Controller
{
    #[OA\Get(
        path: "/products/{product_id}/prices",
        summary: "Listar precios del producto",
        description: "Retorna todos los precios de un producto en diferentes monedas",
        tags: ["Precios de Productos"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "product_id",
                in: "path",
                required: true,
                description: "ID del producto",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Precios del producto",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "base_price", type: "object"),
                                new OA\Property(property: "prices", type: "array", items: new OA\Items(type: "object"))
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: "No autenticado"),
            new OA\Response(response: 404, description: "Producto no encontrado")
        ]
    )]
    public function index(string $productId): JsonResponse
    {
        $product = Product::with('baseCurrency')->findOrFail($productId);
        
        $prices = $product->prices()->with('currency')->get();
        
        return response()->json([
            'success' => true,
            'data' => [
                'base_price' => [
                    'price' => $product->price,
                    'currency' => $product->baseCurrency
                ],
                'prices' => $prices
            ]
        ]);
    }

    #[OA\Post(
        path: "/products/{product_id}/prices",
        summary: "Crear/actualizar precio",
        description: "Crea o actualiza el precio de un producto en una moneda específica",
        tags: ["Precios de Productos"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "product_id",
                in: "path",
                required: true,
                description: "ID del producto",
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["currency_id", "price"],
                properties: [
                    new OA\Property(property: "currency_id", type: "integer", example: 2),
                    new OA\Property(property: "price", type: "number", format: "decimal", example: 89.99)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Precio creado"),
            new OA\Response(response: 200, description: "Precio actualizado"),
            new OA\Response(response: 401, description: "No autenticado"),
            new OA\Response(response: 404, description: "Producto no encontrado"),
            new OA\Response(response: 422, description: "Errores de validación")
        ]
    )]
    public function store(StoreProductPriceRequest $request, string $productId): JsonResponse
    {
        $product = Product::findOrFail($productId);
        
        $productPrice = ProductPrice::updateOrCreate(
            [
                'product_id' => $product->id,
                'currency_id' => $request->validated()['currency_id']
            ],
            [
                'price' => $request->validated()['price']
            ]
        );
        
        $productPrice->load('currency');
        
        $wasRecentlyCreated = $productPrice->wasRecentlyCreated;
        
        return response()->json([
            'success' => true,
            'message' => $wasRecentlyCreated ? 'Precio creado exitosamente' : 'Precio actualizado exitosamente',
            'data' => $productPrice
        ], $wasRecentlyCreated ? 201 : 200);
    }

    #[OA\Delete(
        path: "/products/{product_id}/prices/{price_id}",
        summary: "Eliminar precio",
        description: "Elimina un precio específico de un producto",
        tags: ["Precios de Productos"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "product_id",
                in: "path",
                required: true,
                description: "ID del producto",
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "price_id",
                in: "path",
                required: true,
                description: "ID del precio",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Precio eliminado"),
            new OA\Response(response: 401, description: "No autenticado"),
            new OA\Response(response: 404, description: "Recurso no encontrado")
        ]
    )]
    public function destroy(string $productId, ProductPrice $price): JsonResponse
    {
        $product = Product::findOrFail($productId);

        if ($price->product_id !== $product->id) {
            return response()->json([
                'success' => false,
                'message' => 'Recurso no encontrado',
                'error' => 'El precio no pertenece a este producto'
            ], 404);
        }

        $price->delete();

        return response()->json([
            'success' => true,
            'message' => 'Precio eliminado exitosamente'
        ]);
    }
}
