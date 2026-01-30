<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "Products API",
    description: "API para gestión de productos con soporte multi-moneda",
    contact: new OA\Contact(
        email: "admin@example.com"
    )
)]
#[OA\Server(
    url: "http://localhost/api",
    description: "Servidor de desarrollo"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT",
    description: "Ingresa tu token Bearer"
)]
class OpenApiInfo
{
}
