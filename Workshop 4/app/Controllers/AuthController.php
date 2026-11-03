<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Usuario;
use App\Models\Provincia;
use PDOException;

final class AuthController extends Controller {
    public function loginForm(): void {
        if (isset($_SESSION['usuario_id'])) { $this->redirect('/cursos'); }
        $this->render('auth/login', ['error' => ($_GET['error'] ?? '') === '1', 'registrado' => ($_GET['registrado'] ?? '') === '1']);
    }

    public function login(): void {
        $this->verifyCsrf();
        $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
        $user = Usuario::authenticate($this->text('username'), $password);
        if (!$user) { $this->redirect('/', ['error' => '1']); }
        session_regenerate_id(true);
        $_SESSION['usuario_id'] = (int) $user['id'];
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
        $this->redirect('/cursos');
    }

    public function registerForm(): void {
        $this->render('auth/registro', ['provincias' => Provincia::all(), 'errores' => [], 'usuario' => ['username' => '', 'email' => '', 'provincia_id' => '']]);
    }

    public function register(): void {
        $this->verifyCsrf();
        $user = ['username' => $this->text('username'), 'email' => $this->text('email'), 'provincia_id' => $this->text('provincia_id'), 'password' => is_string($_POST['password'] ?? null) ? $_POST['password'] : ''];
        $provincias = Provincia::all();
        $errors = Usuario::validate($user, $provincias, true);
        if (!$errors) {
            try {
                Usuario::create($user);
                $this->redirect('/', ['registrado' => '1']);
            } catch (PDOException $error) {
                if ($error->getCode() !== '23000') { throw $error; }
                $errors[] = 'El usuario o correo ya está registrado.';
            }
        }
        unset($user['password']);
        http_response_code(422);
        $this->render('auth/registro', ['provincias' => $provincias, 'errores' => $errors, 'usuario' => $user]);
    }

    public function logout(): void {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        $this->redirect('/');
    }
}
