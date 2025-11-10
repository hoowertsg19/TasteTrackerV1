# TasteTracker – API REST para gestión de pedidos en restaurantes

TasteTracker es una API REST construida con Laravel que permite gestionar el menú, los pedidos, los clientes y los empleados de un restaurante. Incluye autenticación con tokens (Sanctum), validación robusta, respuestas estandarizadas y documentación automática.

## Características principales

- Autenticación con Laravel Sanctum (tokens Bearer) y endpoints públicos para registro/login.
- CRUD completo para: menú, empleados, clientes y pedidos.
- Validación sólida mediante Form Requests.
- Respuestas JSON consistentes con API Resources (incluye anidamiento de relaciones).
- Documentación Swagger/OpenAPI generada automáticamente (l5-swagger).
- Subida de imágenes para ítems del menú (disco `public`).
- Suite de pruebas de Feature con 18 tests pasando (Auth, Menu, Pedido, Empleado).
- Versionado de API: v1 bajo el prefijo `/api/v1`.

## Tecnologías utilizadas

- Laravel 12 (PHP ^8.2)
- PHP 8.2+
- MySQL
- Laravel Sanctum
- Swagger/OpenAPI (l5-swagger)
- PHPUnit

## Requisitos previos

- PHP 8.2 o superior
- Composer
- MySQL (u otro motor compatible)
- Servidor web (Apache/Nginx) o `php artisan serve`

## Instalación paso a paso

1) Clonar el repositorio
```bash
git clone <url-del-repo>
cd TasteTrackerV1
```

2) Instalar dependencias
```bash
composer install
```

3) Copiar variables de entorno y generar clave
```bash
cp .env.example .env
php artisan key:generate
```

4) Configurar base de datos en `.env`
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tastetracker
DB_USERNAME=usuario
DB_PASSWORD=clave
```

5) Ejecutar migraciones y seeders
```bash
php artisan migrate
php artisan db:seed
```
Nota: los seeders crean el catálogo de estados de pedido y un menú inicial.

6) Enlazar el almacenamiento público (para imágenes)
```bash
php artisan storage:link
```

7) Iniciar el servidor
```bash
php artisan serve
```
La API estará disponible (por defecto) en `http://localhost:8000`.

## Configuración del entorno

Variables relevantes en `.env`:

- `APP_URL` (ej. `http://localhost:8000`)
- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `SANCTUM_STATEFUL_DOMAINS` (si usas un frontend SPA en el mismo dominio)

Para endpoints protegidos usa el encabezado:
```
Authorization: Bearer <token>
```

## Documentación de la API (Swagger)

Generar/actualizar especificación:
```bash
php artisan l5-swagger:generate
```
UI disponible en:

- `GET {APP_URL}/api/documentation`

Endpoints públicos:

- `POST /api/v1/auth/register`
- `POST /api/v1/auth/login`
- `GET /api/v1/estados-pedido`

Endpoints protegidos (requieren token Bearer): resto de recursos bajo `/api/v1`.

## Endpoints principales

| Método | Ruta                              | Descripción                                   | Auth |
|-------:|-----------------------------------|-----------------------------------------------|:----:|
| POST   | `/api/v1/auth/register`           | Registro de usuario                           |  No  |
| POST   | `/api/v1/auth/login`              | Login (retorna token)                         |  No  |
| GET    | `/api/v1/auth/me`                 | Perfil del usuario autenticado                | Sí   |
| GET    | `/api/v1/menu`                    | Listar menú                                   | Sí   |
| POST   | `/api/v1/menu`                    | Crear ítem de menú                            | Sí   |
| POST   | `/api/v1/menu/{id}/imagen`        | Subir imagen de ítem de menú                  | Sí   |
| GET    | `/api/v1/pedidos`                 | Listar pedidos (con relaciones)               | Sí   |
| POST   | `/api/v1/pedidos`                 | Crear pedido con detalles                     | Sí   |
| GET    | `/api/v1/estados-pedido`          | Listar estados de pedido                      |  No  |

Nota: Existen también los endpoints CRUD para `empleados` y `clientes` bajo `/api/v1/empleados` y `/api/v1/clientes` (protegidos).

## Testing

Ejecutar la suite de pruebas:
```bash
php artisan test
```
Estado actual: 18 tests pasando (cubre autenticación, autorización, CRUD y validación en endpoints clave).

## Estructura del proyecto (carpetas clave)

- `app/Http/Controllers/Api/V1` — Controladores de la API v1
- `app/Http/Requests` — Form Requests para validación
- `app/Http/Resources` — API Resources para respuestas JSON
- `app/Models` — Modelos Eloquent
- `routes/api.php` — Definición de rutas de la API
- `tests/Feature` — Pruebas de Feature (Auth, Menu, Pedido, Empleado)

## Autores

- Hoowerts Gross
- Antony Maltez
- Jorge Rodriguez
- Norman Acevedo

