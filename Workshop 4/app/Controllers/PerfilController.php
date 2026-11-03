<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Usuario;
use App\Models\Provincia;
use PDOException;

final class PerfilController extends Controller {
    public function show(): void {
        $user = Usuario::find($this->authenticate());
        if (!$user) { unset($_SESSION['usuario_id']); $this->redirect('/'); }
        $this->render('perfil/show', ['usuario' => $user, 'provincias' => Provincia::all(), 'guardado' => ($_GET['guardado'] ?? '') === '1', 'errores' => []]);
    }

    public function update(): void {
        $id = $this->authenticate();
        $this->verifyCsrf();
        $user = Usuario::find($id);
        if (!$user) { unset($_SESSION['usuario_id']); $this->redirect('/'); }
        $data = ['username' => $this->text('username'), 'email' => $this->text('email'), 'provincia_id' => $this->text('provincia_id'), 'password' => is_string($_POST['password'] ?? null) ? $_POST['password'] : ''];
        $provincias = Provincia::all();
        $errors = Usuario::validate($data, $provincias, false);
        if (!$errors) {
            try {
                Usuario::update($id, $data);
                $this->redirect('/perfil', ['guardado' => '1']);
            } catch (PDOException $error) {
                if ($error->getCode() !== '23000') { throw $error; }
                $errors[] = 'El nombre de usuario o correo ya está registrado.';
            }
        }
        unset($data['password']);
        http_response_code(422);
        $this->render('perfil/show', ['usuario' => array_merge($user, $data), 'provincias' => $provincias, 'guardado' => false, 'errores' => $errors]);
    }
}
