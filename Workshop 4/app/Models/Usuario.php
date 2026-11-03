<?php
namespace App\Models;

use App\Core\Database;

final class Usuario {
    public static function find(int $id): ?array {
        $query = Database::pdo()->prepare('SELECT u.id, u.username, u.email, u.provincia_id, p.nombre AS provincia FROM usuarios u JOIN provincias p ON p.id = u.provincia_id WHERE u.id = ?');
        $query->execute([$id]);
        return $query->fetch() ?: null;
    }

    public static function authenticate(string $username, string $password): ?array {
        $query = Database::pdo()->prepare('SELECT id, username, password FROM usuarios WHERE username = ? LIMIT 1');
        $query->execute([$username]);
        $user = $query->fetch();
        if (!$user || !(password_verify($password, $user['password']) || hash_equals($user['password'], $password))) {
            return null;
        }
        if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
            $query = Database::pdo()->prepare('UPDATE usuarios SET password = ? WHERE id = ?');
            $query->execute([password_hash($password, PASSWORD_DEFAULT), $user['id']]);
        }
        return $user;
    }

    public static function validate(array $user, array $provincias, bool $registration): array {
        $errors = [];
        if ($user['username'] === '' || mb_strlen($user['username']) > 100) {
            $errors[] = 'El nombre de usuario es obligatorio y debe tener hasta 100 caracteres.';
        }
        if (!filter_var($user['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($user['email']) > 150) {
            $errors[] = 'Ingresa un correo válido de hasta 150 caracteres.';
        }
        $provinciaId = filter_var($user['provincia_id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$provinciaId || !in_array($provinciaId, array_column($provincias, 'id'))) {
            $errors[] = 'Selecciona una provincia válida.';
        }
        if (($registration || $user['password'] !== '') && strlen($user['password']) < 6) {
            $errors[] = 'La contraseña debe tener al menos 6 caracteres.';
        }
        return $errors;
    }

    public static function create(array $user): void {
        $query = Database::pdo()->prepare('INSERT INTO usuarios (username, email, password, provincia_id) VALUES (?, ?, ?, ?)');
        $query->execute([$user['username'], $user['email'], password_hash($user['password'], PASSWORD_DEFAULT), $user['provincia_id']]);
    }

    public static function update(int $id, array $user): void {
        $data = [$user['username'], $user['email'], $user['provincia_id']];
        $sql = 'UPDATE usuarios SET username = ?, email = ?, provincia_id = ?';
        if ($user['password'] !== '') {
            $sql .= ', password = ?';
            $data[] = password_hash($user['password'], PASSWORD_DEFAULT);
        }
        $data[] = $id;
        $query = Database::pdo()->prepare($sql . ' WHERE id = ?');
        $query->execute($data);
    }
}
