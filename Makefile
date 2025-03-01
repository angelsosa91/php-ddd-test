.PHONY: up down build install test db-migrate

# Iniciar contenedores Docker
up:
	docker compose up -d

# Detener contenedores Docker
down:
	docker compose down

# Reconstruir contenedores Docker
build:
	docker compose build

# Instalar dependencias de Composer
install:
	docker compose exec php composer install

# Ejecutar migraciones de Doctrine
db-migrate:
	docker compose exec php vendor/bin/doctrine orm:schema-tool:update --force --complete

# Inicializar proyecto (primera vez)
init: build up install db-migrate

# Ejecutar todas las pruebas (excepto con MySQL)
test:
	docker compose exec php vendor/bin/phpunit

# Ejecutar solo pruebas unitarias
test-unit:
	docker-compose exec php vendor/bin/phpunit --testsuite=Unit

# Ejecutar pruebas de integración (sin MySQL)
test-integration:
	docker-compose exec php vendor/bin/phpunit --testsuite=Integration

# Ejecutar pruebas de integración con MySQL
test-mysql:
	docker-compose exec php vendor/bin/phpunit --testsuite=MySQL