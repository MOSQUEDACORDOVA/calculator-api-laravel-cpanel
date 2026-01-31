# 🧮 Calculator API - Laravel

API REST sencilla para realizar operaciones matemáticas básicas con persistencia en MySQL.

> **⚠️ AVISO DE SEGURIDAD**
> 
> Este proyecto es una **demostración** de cómo implementar una API Laravel en infraestructura legacy como cPanel. 
> **NO incluye autenticación ni medidas de seguridad** intencionalmente para enfocarse en el despliegue.
> 
> **No se recomienda usar en producción** sin implementar:
> - Autenticación (Laravel Sanctum, Passport, etc.)
> - Rate limiting
> - Validación de CORS apropiada
> - HTTPS obligatorio

---

## 📋 Características

- ✅ Operaciones: suma (+), resta (-), multiplicación (*), división (/)
- ✅ Persistencia en MySQL
- ✅ Cache de operaciones (no repite cálculos existentes)
- ✅ Historial de operaciones
- ✅ Límites: máx 3 dígitos enteros + 2 decimales (-999.99 a 999.99)
- ✅ Redondeo estándar a 2 decimales (solo en resultado)
- ✅ Compatible con cPanel (usa .htaccess)
- ✅ Docker para desarrollo local

---

## 🚀 Endpoints de la API

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `POST` | `/api/calculate` | Realizar una operación |
| `GET` | `/api/history` | Listar historial de operaciones |
| `GET` | `/api/history/{id}` | Obtener una operación específica |
| `DELETE` | `/api/history/{id}` | Eliminar una operación |
| `DELETE` | `/api/history` | Eliminar todo el historial |
| `GET` | `/api/health` | Health check del servicio |

### Ejemplos de uso

**Realizar operación:**
```bash
curl -X POST http://localhost:8080/api/calculate \
  -H "Content-Type: application/json" \
  -d '{"num1": 10, "operator": "+", "num2": 5}'
```

**Respuesta:**
```json
{
  "success": true,
  "message": "Operación calculada correctamente",
  "data": {
    "id": 1,
    "result": 15,
    "cached": false
  }
}
```

**Obtener historial:**
```bash
curl http://localhost:8080/api/history
```

---

## 🐳 Desarrollo Local con Docker

### Requisitos
- Docker y Docker Compose instalados

### Comandos

```bash
# Construir la imagen (primera vez o después de cambios en Dockerfile)
docker compose build --no-cache

# Levantar los contenedores
docker compose up -d

# Ver logs
docker logs calculator-api-laravel

# Detener contenedores
docker compose down
```

### Servicios disponibles

| Servicio | URL | Descripción |
|----------|-----|-------------|
| API Laravel | http://localhost:8080 | Aplicación principal |
| phpMyAdmin | http://localhost:8081 | Gestión de MySQL |
| MySQL | localhost:3306 | Base de datos |

### Comandos Artisan dentro del contenedor

```bash
# Ejecutar migraciones
docker exec -it calculator-api-laravel php artisan migrate

# Recrear base de datos (elimina todo)
docker exec -it calculator-api-laravel php artisan migrate:fresh

# Limpiar caché
docker exec -it calculator-api-laravel php artisan cache:clear

# Generar APP_KEY
docker exec -it calculator-api-laravel php artisan key:generate

# Regenerar autoload de Composer
docker exec -it calculator-api-laravel composer dump-autoload

# Entrar al contenedor (bash interactivo)
docker exec -it calculator-api-laravel bash
```

---

## 📦 Despliegue en cPanel (Producción)

### Paso 1: Preparar archivos

1. Comprime todo el proyecto (excepto `vendor/`, `node_modules/`, `.git/`)
2. Sube el archivo ZIP a cPanel via **File Manager**
3. Extrae en el directorio deseado (ej: `public_html/calculator-api/`)

### Paso 2: Configurar base de datos

1. En cPanel → **MySQL Databases**:
   - Crear base de datos: `tu_usuario_calculator`
   - Crear usuario y asignar permisos

2. Editar `.env` con los datos de MySQL de cPanel:
```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=tu_usuario_calculator
DB_USERNAME=tu_usuario_db
DB_PASSWORD=tu_password_db
```

### Paso 3: Instalar dependencias

En cPanel → **Terminal** (o vía SSH):

```bash
cd public_html/calculator-api

# Instalar dependencias
composer install --no-dev --optimize-autoloader

# Regenerar autoload
composer dump-autoload

# Generar APP_KEY (si no existe)
php artisan key:generate

# Ejecutar migraciones
php artisan migrate

# Limpiar y cachear configuración
php artisan config:cache
php artisan route:cache
```

### Paso 4: Configurar permisos

```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### Paso 5: Configurar dominio/subdominio

En cPanel, apunta el dominio/subdominio a la carpeta `public/` del proyecto.

**Opción A - Subdominio:**
- Crear subdominio: `api.tudominio.com`
- Document Root: `public_html/calculator-api/public`

**Opción B - Subdirectorio:**
- Los archivos `.htaccess` ya están configurados para redirigir a `public/`

---

## 📁 Estructura del Proyecto

```
├── app/
│   ├── Http/Controllers/
│   │   └── OperationController.php   # Lógica de la API
│   └── Models/
│       └── Operation.php             # Modelo de operaciones
├── database/
│   └── migrations/                   # Migraciones de BD
├── routes/
│   └── api.php                       # Rutas de la API
├── public/
│   ├── index.php                     # Entry point
│   └── .htaccess                     # Rewrite rules
├── .htaccess                         # Redirect a public/
├── docker-compose.yml                # Config Docker
├── Dockerfile                        # Imagen Docker
└── .env.example                      # Variables de entorno
```

---

## 🔧 Variables de Entorno

Copia `.env.example` a `.env` y configura:

```env
APP_NAME=CalculatorAPI
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=calculator_api
DB_USERNAME=usuario
DB_PASSWORD=password
```

---

## 📝 Notas Técnicas

- **Framework:** Laravel 12
- **PHP:** 8.2+
- **Base de datos:** MySQL 8.0
- **Límites de números:** -999.99 a 999.99
- **Decimales:** máximo 2, redondeo estándar (round) solo en el resultado final
- **Cache de operaciones:** Si una operación ya existe, retorna el resultado almacenado

---

## 📄 Licencia

MIT

