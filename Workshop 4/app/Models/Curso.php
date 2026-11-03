<?php
namespace App\Models;

use App\Core\Database;

final class Curso {
    public static function all(): array {
        return Database::pdo()->query('SELECT c.*, ca.nombre AS carrera FROM cursos c JOIN carreras ca ON ca.id = c.carrera_id ORDER BY c.nombre')->fetchAll();
    }

    public static function find(int $id): ?array {
        $query = Database::pdo()->prepare('SELECT * FROM cursos WHERE id = ?');
        $query->execute([$id]);
        return $query->fetch() ?: null;
    }

    public static function validate(array $curso, array $carreras): array {
        $errors = [];
        if ($curso['nombre'] === '' || mb_strlen($curso['nombre']) > 150) {
            $errors[] = 'El nombre es obligatorio y debe tener hasta 150 caracteres.';
        }
        if (!preg_match('/^[A-Za-z0-9_-]{1,30}$/', $curso['codigo'])) {
            $errors[] = 'El código debe tener hasta 30 caracteres: letras, números, guiones o guiones bajos.';
        }
        $carreraId = filter_var($curso['carrera_id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$carreraId || !in_array($carreraId, array_column($carreras, 'id'))) {
            $errors[] = 'Selecciona una carrera válida.';
        }
        if (filter_var($curso['creditos'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 30]]) === false) {
            $errors[] = 'Los créditos deben ser un número entero entre 1 y 30.';
        }
        if ($curso['descripcion'] === '' || mb_strlen($curso['descripcion']) > 5000) {
            $errors[] = 'La descripción es obligatoria y debe tener hasta 5000 caracteres.';
        }
        return $errors;
    }

    public static function save(array $curso, ?int $id): void {
        $data = [$curso['nombre'], $curso['codigo'], $curso['carrera_id'], $curso['creditos'], $curso['descripcion']];
        if ($id !== null) {
            $data[] = $id;
            $query = Database::pdo()->prepare('UPDATE cursos SET nombre = ?, codigo = ?, carrera_id = ?, creditos = ?, descripcion = ? WHERE id = ?');
        } else {
            $query = Database::pdo()->prepare('INSERT INTO cursos (nombre, codigo, carrera_id, creditos, descripcion) VALUES (?, ?, ?, ?, ?)');
        }
        $query->execute($data);
    }

    public static function delete(int $id): bool {
        $query = Database::pdo()->prepare('DELETE FROM cursos WHERE id = ?');
        $query->execute([$id]);
        return $query->rowCount() > 0;
    }
}
