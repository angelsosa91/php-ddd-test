# Registro de Usuarios con DDD y Clean Architecture

Este proyecto implementa un registro de usuarios siguiendo los principios de Domain-Driven Design (DDD) y Clean Architecture, usando PHP sin frameworks (excepto Doctrine para la persistencia de datos).

## Requisitos

- Docker y Docker Compose
- Git

## Instalación

1. Clonar el repositorio:
```bash
git clone https://github.com/angelsosa91/php-ddd-test.git
cd php-ddd-test
```

2. Iniciar el proyecto usando Make:
```bash
make init
```

o manualmente paso a paso:
```bash
make build 
make up 
make install 
make db-migrate
```

Este comando:
- Construye las imágenes de Docker
- Inicia los contenedores
- Instala las dependencias de Composer
- Ejecuta las migraciones de la base de datos

## Estructura del Proyecto

```
proyecto/
├── src/
│   ├── Domain/            # Capa de Dominio (Entidades, Value Objects)
│   ├── Application/       # Capa de Aplicación (Casos de Uso, DTOs)
│   └── Infrastructure/    # Capa de Infraestructura (Persistencia, Controladores)
├── tests/                 # Pruebas unitarias y de integración
├── config/                # Configuración (Doctrine)
├── public/                # Punto de entrada de la aplicación
└── docker/                # Configuración de Docker
```

## Arquitectura

El proyecto sigue los principios de:

- **Domain-Driven Design (DDD)**: Modelado del dominio con entidades y value objects
- **Clean Architecture**: Capas bien definidas con dependencias dirigidas hacia el dominio
- **Ports and Adapters**: Interfaces en el dominio, implementaciones en la infraestructura

## API Endpoints

### Registrar un nuevo usuario

```
POST /api/users
Endpoint: http://localhost:8000/api/users
```

Ejemplo de payload:
```json
{
  "name": "Angel Maldonado",
  "email": "angel.sosa911@gmail.com",
  "password": "Password123!"
}
```

Respuesta exitosa (201 Created):
```json
{
  "success": true,
  "data": {
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "name": "Angel Maldonado",
    "email": "angel.sosa911@gmail.com",
    "created_at": "2025-03-01 23:59:59"
  },
  "code": 201
}
```

### Listar usuarios

```
GET /api/users
Endpoint: http://localhost:8000/api/users
```

Respuesta exitosa (200 OK):
```json
{
    "success": true,
    "data": {
        "users": [
            {
                "id": "63a2ef79-5667-486f-92a6-36f867b9f818",
                "name": "Julian Draxler",
                "email": "jdrax@example.com",
                "created_at": "2025-03-01 14:01:33"
            },
            {
                "id": "f45d8ac5-270e-472b-bf24-47468b1868f2",
                "name": "John Temper",
                "email": "john1@example.com",
                "created_at": "2025-03-01 13:54:08"
            }
        ]
    },
    "code": 200
}
```

### Listar usuarios por Id

```
GET /api/users/{id}
Endpoint: http://localhost:8000/api/users/{id}

Ejemplo: http://localhost:8000/api/users/63a2ef79-5667-486f-92a6-36f867b9f818
```

Respuesta exitosa (200 OK):
```json
{
    "success": true,
    "data": {
        "users": [
            {
                "id": "63a2ef79-5667-486f-92a6-36f867b9f818",
                "name": "Julian Draxler",
                "email": "jdrax@example.com",
                "created_at": "2025-03-01 14:01:33"
            }
        ]
    },
    "code": 200
}
```

Respuesta error (400 Bad Request)
```json
{
    "success": false,
    "message": "ID de usuario inválido",
    "code": 400
}
```

## Comandos Útiles

- `make up`: Inicia los contenedores Docker
- `make down`: Detiene los contenedores Docker
- `make test`: Ejecuta las pruebas PHPUnit
- `make db-migrate`: Ejecuta las migraciones de Doctrine

## Pruebas

Para ejecutar todas las pruebas (Sin MySQL):

```bash
make test
```

Ejecutar solo las pruebas unitarias
```bash
make test-unit
```

Ejecutar solo las pruebas de integración (sin MySQL)
```bash
make test-integration
```

Ejecutar las pruebas específicas de MySQL
```bash
make test-mysql
```

## Notas de Implementación

- La entidad `User` es inmutable y utiliza Value Objects para todos sus atributos
- Las contraseñas se almacenan utilizando bcrypt para el hash
- Los eventos de dominio se utilizan para operaciones post-registro
- La arquitectura está diseñada para ser extensible y fácil de mantener