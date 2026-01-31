Comandos utiles:

docker compose build --no-cache
docker compose up -d

# Ejecutar migraciones
docker exec -it calculator-api-laravel php artisan migrate

# Eliminar todo
docker exec -it calculator-api-laravel php artisan migrate:fresh

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



tareas pendientes:

- aclarar que esto es solo una demostracion de como implemntar una api laravel en una infra legacy como capnel que no puse seguridad para enfocarme en el despleigue pero que no es recomendaro par aproduccion
- dejar esa acarlarcion tambien e el archivo de routes/api


- Corregir errores basicos al solicitar , como que no exista informacion , etc 
No debe manejar tantos digitos ni tantos decimales,, poner limites 
- no DEBRIAN HABER OPERACIONES REPETIDAS

- quiero pdoer eliminar uno solo 
