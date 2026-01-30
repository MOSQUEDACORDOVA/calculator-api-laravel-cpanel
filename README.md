# 🚀 Products API - Laravel RESTful

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/MySQL-8.4-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Docker-Sail-2496ED?style=for-the-badge&logo=docker&logoColor=white" alt="Docker">
</p>

---

## 📋 Descripción

API RESTful desarrollada en **Laravel 12** para la gestión completa de productos con soporte **multi-moneda**. Incluye autenticación mediante tokens (Laravel Sanctum), documentación Swagger/OpenAPI, y está containerizada con Docker usando Laravel Sail.

---

## ✨ Características Implementadas

### ✅ Requisitos Base
- [x] CRUD completo de productos
- [x] Campos: nombre, descripción, precio, costo de impuestos, costo de fabricación
- [x] Registro de precios en diferentes divisas
- [x] Respuestas en formato JSON
- [x] Eloquent ORM para interacción con base de datos

### 🌟 Características Extra (Valor Agregado)
- [x] **Autenticación API** con Laravel Sanctum (tokens Bearer)
- [x] **Documentación OpenAPI/Swagger** completa y auto-generada
- [x] **Form Requests** con validación robusta y mensajes en español
- [x] **Paginación** en listados con límite configurable
- [x] **Respuestas estandarizadas** con estructura consistente
- [x] **Docker Compose** con Laravel Sail (MySQL, Redis)
- [x] **Seeders** para datos iniciales (monedas, usuario de prueba)
- [x] **Relaciones Eloquent** bien definidas (HasMany, BelongsTo)
- [x] **Validación inteligente** que previene duplicar precio base
- [x] **Endpoint adicional** para eliminar precios de productos
- [x] **Endpoint de monedas** para consultar divisas disponibles

---

## 🗃️ Modelo de Datos

### Diagrama ER

```
┌─────────────────┐       ┌─────────────────┐       ┌─────────────────┐
│    PRODUCTS     │       │   CURRENCIES    │       │ PRODUCT_PRICES  │
├─────────────────┤       ├─────────────────┤       ├─────────────────┤
│ id              │───┐   │ id              │───┐   │ id              │
│ name            │   │   │ name            │   │   │ product_id (FK) │
│ description     │   │   │ symbol          │   ├──▶│ currency_id (FK)│
│ price           │   │   │ exchange_rate   │   │   │ price           │
│ currency_id (FK)│◀──┼───│ created_at      │   │   │ created_at      │
│ tax_cost        │   │   │ updated_at      │   │   │ updated_at      │
│ manufacturing_  │   │   └─────────────────┘   │   └─────────────────┘
│   cost          │   │                         │
│ created_at      │   └─────────────────────────┘
│ updated_at      │
└─────────────────┘
```

### Tablas

#### 📦 Tabla: Products

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | integer | Identificador único |
| name | string | Nombre del producto |
| description | text | Descripción (opcional) |
| price | decimal(10,2) | Precio en divisa base |
| currency_id | integer | FK a currencies |
| tax_cost | decimal(10,2) | Costo de impuestos |
| manufacturing_cost | decimal(10,2) | Costo de fabricación |
| created_at | timestamp | Fecha de creación |
| updated_at | timestamp | Fecha de actualización |

#### 💱 Tabla: Currencies

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | integer | Identificador único |
| name | string | Nombre (ej: "US Dollar") |
| symbol | string | Símbolo (ej: "USD") |
| exchange_rate | decimal(10,4) | Tasa de cambio |
| created_at | timestamp | Fecha de creación |
| updated_at | timestamp | Fecha de actualización |

#### 💰 Tabla: Product Prices

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | integer | Identificador único |
| product_id | integer | FK a products (cascada) |
| currency_id | integer | FK a currencies (cascada) |
| price | decimal(10,2) | Precio en divisa específica |
| created_at | timestamp | Fecha de creación |
| updated_at | timestamp | Fecha de actualización |

---

## 🔌 Endpoints API

### Autenticación
| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| `POST` | `/api/login` | Iniciar sesión | ❌ |
| `POST` | `/api/logout` | Cerrar sesión | ✅ |
| `GET` | `/api/me` | Información del usuario actual | ✅ |

### Productos
| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| `GET` | `/api/products` | Listar productos (paginado) | ✅ |
| `POST` | `/api/products` | Crear producto | ✅ |
| `GET` | `/api/products/{id}` | Ver producto | ✅ |
| `PUT` | `/api/products/{id}` | Actualizar producto | ✅ |
| `DELETE` | `/api/products/{id}` | Eliminar producto | ✅ |

### Precios de Productos
| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| `GET` | `/api/products/{id}/prices` | Listar precios del producto | ✅ |
| `POST` | `/api/products/{id}/prices` | Crear/Actualizar precio | ✅ |
| `DELETE` | `/api/products/{id}/prices/{priceId}` | Eliminar precio | ✅ |

### Monedas
| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| `GET` | `/api/currencies` | Listar monedas disponibles | ✅ |

---

## 🛠️ Instalación y Configuración

### Requisitos Previos
- Docker Desktop
- Git

### Pasos de Instalación

```bash
# 1. Clonar el repositorio
git clone https://github.com/tu-usuario/laravel-products-api.git
cd laravel-products-api

# 2. Copiar archivo de entorno
cp .env.example .env

# 3. Instalar dependencias (usando Docker)
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs

# 4. Iniciar contenedores
./vendor/bin/sail up -d

# 5. Generar key de la aplicación
./vendor/bin/sail artisan key:generate

# 6. Ejecutar migraciones y seeders
./vendor/bin/sail artisan migrate:fresh --seed

# 7. Generar documentación Swagger
./vendor/bin/sail artisan l5-swagger:generate
```

### Variables de Entorno Principales
```env
APP_URL=http://localhost
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password
```

---

## 🔐 Autenticación

La API utiliza **Laravel Sanctum** para autenticación mediante tokens Bearer.

### Usuario de Prueba
```
Email: tester@example.com
Password: password123
```

### Obtener Token
```bash
curl -X POST http://localhost/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email": "tester@example.com", "password": "password123"}'
```

### Usar Token
```bash
curl -X GET http://localhost/api/products \
  -H "Authorization: Bearer {tu_token}" \
  -H "Accept: application/json"
```

---

## 📚 Ejemplos de Uso

### Crear Producto
```bash
curl -X POST http://localhost/api/products \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Laptop Gaming",
    "description": "Laptop para gaming de alta gama",
    "price": 1299.99,
    "currency_id": 1,
    "tax_cost": 129.99,
    "manufacturing_cost": 800.00
  }'
```

**Respuesta:**
```json
{
  "success": true,
  "message": "Producto creado exitosamente",
  "data": {
    "id": 1,
    "name": "Laptop Gaming",
    "description": "Laptop para gaming de alta gama",
    "price": "1299.99",
    "tax_cost": "129.99",
    "manufacturing_cost": "800.00",
    "currency": {
      "id": 1,
      "name": "US Dollar",
      "symbol": "USD"
    }
  }
}
```

### Agregar Precio en Otra Moneda
```bash
curl -X POST http://localhost/api/products/1/prices \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "currency_id": 2,
    "price": 22749.99
  }'
```

### Listar Productos (Paginado)
```bash
curl -X GET "http://localhost/api/products?per_page=10" \
  -H "Authorization: Bearer {token}"
```

---

## 📖 Documentación API

### Swagger UI
Disponible en: `http://localhost/api/documentation`

### OpenAPI JSON
Disponible en: `storage/api-docs/api-docs.json`

---

## 🏗️ Estructura del Proyecto

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php       # Autenticación
│   │   ├── ProductController.php    # CRUD Productos
│   │   ├── ProductPriceController.php # Precios
│   │   └── CurrencyController.php   # Monedas
│   └── Requests/
│       ├── StoreProductRequest.php      # Validación crear
│       ├── UpdateProductRequest.php     # Validación actualizar
│       └── StoreProductPriceRequest.php # Validación precios
├── Models/
│   ├── Product.php       # Modelo Producto
│   ├── Currency.php      # Modelo Moneda
│   ├── ProductPrice.php  # Modelo Precio
│   └── User.php          # Modelo Usuario
└── OpenApi/
    └── OpenApiInfo.php   # Configuración Swagger

database/
├── migrations/           # Migraciones de BD
└── seeders/
    ├── DatabaseSeeder.php
    └── CurrencySeeder.php  # Monedas iniciales

routes/
└── api.php              # Definición de rutas API
```

---

## 🪙 Monedas Preconfiguradas

| ID | Nombre | Símbolo | Tasa de Cambio |
|----|--------|---------|----------------|
| 1 | US Dollar | USD | 1.0000 |
| 2 | Mexican Peso | MXN | 17.5000 |
| 3 | Euro | EUR | 0.9200 |
| 4 | Canadian Dollar | CAD | 1.3500 |
| 5 | British Pound | GBP | 0.7900 |

---

## 🧪 Testing

```bash
# Ejecutar todas las pruebas
./vendor/bin/sail artisan test

# Ejecutar PHPUnit directamente
./vendor/bin/sail exec laravel.test ./vendor/bin/phpunit
```

---

## 🐳 Comandos Docker Útiles

```bash
# Iniciar servicios
./vendor/bin/sail up -d

# Detener servicios
./vendor/bin/sail down

# Ver logs
./vendor/bin/sail logs -f

# Acceder al contenedor
./vendor/bin/sail shell

# Ejecutar artisan
./vendor/bin/sail artisan [comando]

# Ejecutar composer
./vendor/bin/sail composer [comando]
```

---

## 📝 Decisiones Técnicas

| Decisión | Justificación |
|----------|---------------|
| **Laravel Sanctum** | Autenticación ligera y segura para APIs, ideal para SPAs y móviles |
| **Form Requests** | Separación de responsabilidades, validación reutilizable |
| **updateOrCreate** | Permite crear o actualizar precios en una sola operación |
| **Soft constraints** | No usar FK en products.currency_id para flexibilidad |
| **Paginación configurable** | Permite al cliente definir tamaño de página (máx 100) |
| **Docker Sail** | Desarrollo consistente entre entornos, fácil onboarding |

---

## 🔒 Seguridad Implementada

- ✅ Autenticación Bearer Token (Sanctum)
- ✅ Validación de datos en todas las entradas
- ✅ Protección CSRF deshabilitada para rutas API
- ✅ Middleware de autenticación en rutas protegidas
- ✅ Mensajes de error sin exponer información sensible
- ✅ Tokens revocables al cerrar sesión

---

## 👤 Autor

<p align="center">
  <strong>Desarrollado por <a href="https://www.mosquedacordova.com/" target="_blank">Isaac Mosqueda</a></strong>
</p>
