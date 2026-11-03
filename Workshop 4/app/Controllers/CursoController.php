<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Curso;
use App\Models\Carrera;
use PDOException;

final class CursoController extends Controller {
    public function index(): void {
        $this->authenticate();
        $messages = ['creado' => 'Curso creado correctamente.', 'editado' => 'Curso actualizado correctamente.', 'eliminado' => 'Curso eliminado correctamente.', 'no-encontrado' => 'El curso solicitado ya no existe.'];
        $state = is_string($_GET['estado'] ?? null) ? $_GET['estado'] : '';
        $this->render('cursos/index', ['cursos' => Curso::all(), 'mensaje' => $messages[$state] ?? '']);
    }

    public function create(): void {
        $this->authenticate();
        $this->form(['nombre' => '', 'codigo' => '', 'carrera_id' => '', 'creditos' => '', 'descripcion' => ''], null);
    }

    public function edit(string $id): void {
        $this->authenticate();
        $courseId = $this->courseId($id);
        if ($courseId === null) { return; }
        $this->form(Curso::find($courseId), $courseId);
    }

    public function store(): void {
        $this->save(null);
    }

    public function update(string $id): void {
        $this->authenticate();
        $courseId = $this->courseId($id);
        if ($courseId !== null) { $this->save($courseId); }
    }

    private function courseId(string $id): ?int {
        $value = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$value || !Curso::find($value)) {
            $this->error(404, 'Curso no encontrado.');
            return null;
        }
        return $value;
    }

    private function form(array $curso, ?int $id, array $errores = []): void {
        $this->render('cursos/form', ['curso' => $curso, 'id' => $id, 'errores' => $errores, 'carreras' => Carrera::all(), 'titulo' => $id ? 'Editar Curso' : 'Crear Curso', 'action' => url($id ? '/cursos/' . $id . '/editar' : '/cursos')]);
    }

    private function save(?int $id): void {
        $this->authenticate();
        $this->verifyCsrf();
        $course = ['nombre' => $this->text('nombre'), 'codigo' => $this->text('codigo'), 'descripcion' => $this->text('descripcion'), 'carrera_id' => $this->text('carrera_id'), 'creditos' => $this->text('creditos')];
        $errors = Curso::validate($course, Carrera::all());
        if (!$errors) {
            try {
                Curso::save($course, $id);
                $this->redirect('/cursos', ['estado' => $id ? 'editado' : 'creado']);
            } catch (PDOException $error) {
                if ($error->getCode() !== '23000') { throw $error; }
                $errors[] = 'El código del curso ya está registrado o la carrera ya no existe.';
            }
        }
        http_response_code(422);
        $this->form($course, $id, $errors);
    }

    public function delete(string $id): void {
        $this->authenticate();
        $this->verifyCsrf();
        $courseId = $this->courseId($id);
        if ($courseId === null) { return; }
        $deleted = Curso::delete($courseId);
        $this->redirect('/cursos', ['estado' => $deleted ? 'eliminado' : 'no-encontrado']);
    }
}
