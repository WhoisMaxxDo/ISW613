<?php
declare(strict_types=1);

require __DIR__ . '/../app/bootstrap.php';

use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\CursoController;
use App\Controllers\PerfilController;
use App\Controllers\ErrorController;

$router = new Router();
$router->get('/', [AuthController::class, 'loginForm']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/registro', [AuthController::class, 'registerForm']);
$router->post('/registro', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->get('/perfil', [PerfilController::class, 'show']);
$router->post('/perfil', [PerfilController::class, 'update']);
$router->get('/cursos', [CursoController::class, 'index']);
$router->get('/cursos/crear', [CursoController::class, 'create']);
$router->post('/cursos', [CursoController::class, 'store']);
$router->get('/cursos/{id}/editar', [CursoController::class, 'edit']);
$router->post('/cursos/{id}/editar', [CursoController::class, 'update']);
$router->post('/cursos/{id}/eliminar', [CursoController::class, 'delete']);

$route = $_GET['r'] ?? '/';
try {
    if (!is_string($route)) {
        (new ErrorController())->error(400, 'Ruta inválida.');
    } else {
        $router->dispatch($_SERVER['REQUEST_METHOD'], $route);
    }
} catch (Throwable $error) {
    error_log((string) $error);
    (new ErrorController())->error(500, 'No se pudo completar la solicitud. Verifica que MySQL esté activo y que database.sql esté importado.');
}
