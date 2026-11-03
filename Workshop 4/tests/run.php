<?php
declare(strict_types=1);

require __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;
use App\Core\Router;
use App\Controllers\CursoController;
use App\Controllers\PerfilController;
use App\Controllers\AuthController;
use App\Models\Curso;
use App\Models\Usuario;
use App\Models\Provincia;
use App\Models\Carrera;

// Base temporal en memoria: estas pruebas no modifican MySQL.
$pdo = new PDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
$property = new ReflectionProperty(Database::class, 'pdo');
$property->setValue(null, $pdo);
$pdo->exec('PRAGMA foreign_keys = ON');
$pdo->exec('CREATE TABLE provincias (id INTEGER PRIMARY KEY, nombre TEXT NOT NULL)');
$pdo->exec('CREATE TABLE carreras (id INTEGER PRIMARY KEY, nombre TEXT NOT NULL)');
$pdo->exec('CREATE TABLE usuarios (id INTEGER PRIMARY KEY, username TEXT UNIQUE, email TEXT UNIQUE, password TEXT, provincia_id INTEGER REFERENCES provincias(id))');
$pdo->exec('CREATE TABLE cursos (id INTEGER PRIMARY KEY, nombre TEXT, codigo TEXT UNIQUE, carrera_id INTEGER REFERENCES carreras(id), creditos INTEGER, descripcion TEXT)');
$pdo->exec("INSERT INTO provincias VALUES (1, 'San José')");
$pdo->exec("INSERT INTO carreras VALUES (1, 'Ingeniería de Software')");
$count = 0;
function check(bool $condition, string $message): void {
    global $count;
    if (!$condition) { throw new RuntimeException($message); }
    $count++;
}

$course = ['nombre' => 'Programación', 'codigo' => 'ISW-TEST', 'carrera_id' => '1', 'creditos' => '4', 'descripcion' => '<script>alert(1)</script>'];
check(Curso::validate($course, Carrera::all()) === [], 'Curso válido');
foreach (['0', '31', '1.5', 'abc', ''] as $credits) {
    check(Curso::validate(array_merge($course, ['creditos' => $credits]), Carrera::all()) !== [], 'Rechazar créditos inválidos');
}
foreach (['0', '999', '1e0', 'abc'] as $career) {
    check(Curso::validate(array_merge($course, ['carrera_id' => $career]), Carrera::all()) !== [], 'Rechazar carrera inválida');
}
check(Curso::validate(array_merge($course, ['nombre' => '', 'codigo' => '!', 'descripcion' => '']), Carrera::all()) !== [], 'Campos obligatorios');
Curso::save($course, null);
$id = (int) $pdo->lastInsertId();
check(count(Curso::all()) === 1, 'Listado de cursos');
check(Curso::find($id)['codigo'] === 'ISW-TEST', 'Buscar curso');
$course['nombre'] = 'Programación Web';
Curso::save($course, $id);
check(Curso::find($id)['nombre'] === 'Programación Web', 'Editar curso');
try {
    Curso::save($course, null);
    check(false, 'Debe rechazar código duplicado');
} catch (PDOException $error) {
    check($error->getCode() === '23000', 'Código único');
}

$user = ['username' => 'estudiante', 'email' => 'estudiante@example.com', 'password' => 'secreto123', 'provincia_id' => '1'];
check(Usuario::validate($user, Provincia::all(), true) === [], 'Usuario válido');
check(Usuario::validate(array_merge($user, ['email' => 'incorrecto', 'password' => '123']), Provincia::all(), true) !== [], 'Validar registro');
Usuario::create($user);
$userId = (int) $pdo->lastInsertId();
check(Usuario::authenticate('estudiante', 'incorrecta') === null, 'Rechazar contraseña incorrecta');
check((int) Usuario::authenticate('estudiante', 'secreto123')['id'] === $userId, 'Autenticación');
check(password_verify('secreto123', $pdo->query('SELECT password FROM usuarios')->fetchColumn()), 'Contraseña protegida');
$user['password'] = '';
$user['email'] = 'nuevo@example.com';
check(Usuario::validate($user, Provincia::all(), false) === [], 'Contraseña opcional en perfil');
Usuario::update($userId, $user);
check(Usuario::find($userId)['email'] === 'nuevo@example.com', 'Editar perfil');
check(Usuario::authenticate('estudiante', 'secreto123') !== null, 'Conservar contraseña');
$user['password'] = 'nueva123';
Usuario::update($userId, $user);
check(Usuario::authenticate('estudiante', 'nueva123') !== null, 'Cambiar contraseña');

$_SESSION['usuario_id'] = $userId;
$router = new Router();
$router->get('/', [AuthController::class, 'registerForm']);
$router->get('/perfil', [PerfilController::class, 'show']);
$router->get('/cursos', [CursoController::class, 'index']);
$router->get('/cursos/crear', [CursoController::class, 'create']);
$router->get('/cursos/{id}/editar', [CursoController::class, 'edit']);
$router->post('/cursos', [CursoController::class, 'store']);
$router->post('/cursos/{id}/eliminar', [CursoController::class, 'delete']);
function request(Router $router, string $method, string $path): string {
    http_response_code(200);
    ob_start();
    $router->dispatch($method, $path);
    return ob_get_clean();
}
check(str_contains(request($router, 'GET', '/'), 'Crear cuenta'), 'Vista de registro');
check(str_contains(request($router, 'GET', '/perfil'), 'nuevo@example.com'), 'Vista de perfil');
$html = request($router, 'GET', '/cursos');
check(str_contains($html, '&lt;script&gt;') && !str_contains($html, '<script>'), 'Escape XSS');
check(str_contains($html, 'method="post"'), 'Formulario de eliminación POST');
check(str_contains(request($router, 'GET', '/cursos/crear'), 'Crear Curso'), 'Formulario de creación');
check(str_contains(request($router, 'GET', '/cursos/' . $id . '/editar'), 'Programación Web'), 'Formulario de edición');
request($router, 'GET', '/cursos/abc/editar');
check(http_response_code() === 404, 'ID inválido devuelve 404');
request($router, 'GET', '/cursos/999/editar');
check(http_response_code() === 404, 'Curso inexistente devuelve 404');
request($router, 'GET', '/no-existe');
check(http_response_code() === 404, 'Ruta inexistente devuelve 404');
request($router, 'GET', '/cursos/' . $id . '/eliminar');
check(http_response_code() === 405 && Curso::find($id) !== null, 'GET no elimina');
$_POST = ['csrf' => $_SESSION['csrf'], 'nombre' => '', 'creditos' => '0'];
request($router, 'POST', '/cursos');
check(http_response_code() === 422 && count(Curso::all()) === 1, 'POST inválido no guarda');
check(Curso::delete($id), 'Eliminar curso');
check(Curso::find($id) === null && Curso::all() === [], 'Confirmar eliminación');
check(!Curso::delete($id), 'Eliminar curso inexistente');
echo "Correcto: $count comprobaciones.\n";
