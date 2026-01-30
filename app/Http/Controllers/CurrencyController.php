<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class CurrencyController extends Controller
{
    #[OA\Get(
        path: "/currencies",
        summary: "Listar monedas",
        description: "Retorna todas las monedas disponibles",
        tags: ["Monedas"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Lista de monedas",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "id", type: "integer"),
                                    new OA\Property(property: "code", type: "string", example: "USD"),
                                    new OA\Property(property: "name", type: "string", example: "US Dollar"),
                                    new OA\Property(property: "symbol", type: "string", example: "$"),
                                    new OA\Property(property: "exchange_rate", type: "number", example: 1.0)
                                ]
                            )
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: "No autenticado")
        ]
    )]
    public function index(): JsonResponse
    {
        $currencies = Currency::all()->makeVisible('exchange_rate');
        
        return response()->json([
            'success' => true,
            'data' => $currencies
        ]);
    }
}
