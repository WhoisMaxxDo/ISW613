<?php
namespace App\Models;

use App\Core\Database;

final class Provincia {
    public static function all(): array {
        return Database::pdo()->query('SELECT id, nombre FROM provincias ORDER BY nombre')->fetchAll();
    }
}
