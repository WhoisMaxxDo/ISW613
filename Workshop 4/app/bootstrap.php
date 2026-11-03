<?php
declare(strict_types=1);

spl_autoload_register(function (string $class): void {
    if (str_starts_with($class, 'App\\')) {
        $path = __DIR__ . '/' . str_replace('\\', '/', substr($class, 4)) . '.php';
        if (is_file($path)) {
            require $path;
        }
    }
});
require __DIR__ . '/helpers.php';
if (session_status() === PHP_SESSION_NONE) {
    session_name('workshop4');
    session_start();
}
$_SESSION['csrf'] ??= bin2hex(random_bytes(32));
