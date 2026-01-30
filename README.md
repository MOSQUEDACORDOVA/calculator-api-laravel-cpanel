Comandos utiles:

docker compose build --no-cache
docker compose up -d

# Ejecutar migraciones
docker exec -it calculator-api-laravel php artisan migrate

# Ejecutar migraciones con seeders
docker exec -it calculator-api-laravel php artisan migrate --seed

# Limpiar caché
docker exec -it calculator-api-laravel php artisan cache:clear

# Generar key
docker exec -it calculator-api-laravel php artisan key:generate

# Cualquier comando artisan
docker exec -it calculator-api-laravel php artisan <comando>

# Entrar al contenedor (bash interactivo)
docker exec -it calculator-api-laravel bash