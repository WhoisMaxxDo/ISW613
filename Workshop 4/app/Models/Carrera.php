<?php
namespace App\Models;

use App\Core\Database;

final class Carrera {
    public static function all(): array {
        return Database::pdo()->query('SELECT id, nombre FROM carreras ORDER BY nombre')->fetchAll();
    }
}
