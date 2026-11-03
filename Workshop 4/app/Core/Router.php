<?php
namespace App\Core;

use App\Controllers\ErrorController;

final class Router {
    private array $routes = [];

    public function get(string $path, array $handler): void {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, array $handler): void {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(string $method, string $uri): void {
        $allowed = [];
        foreach ($this->routes as $verb => $routes) {
            foreach ($routes as $path => [$class, $action]) {
                $pattern = '#^' . preg_replace('/\{\w+\}/', '([^/]+)', $path) . '$#';
                if (preg_match($pattern, $uri, $matches)) {
                    $allowed[] = $verb;
                    if ($verb === $method) {
                        array_shift($matches);
                        (new $class())->$action(...$matches);
                        return;
                    }
                }
            }
        }
        if ($allowed) {
            header('Allow: ' . implode(', ', array_unique($allowed)));
        }
        (new ErrorController())->error($allowed ? 405 : 404, $allowed ? 'Método no permitido.' : 'Página no encontrada.');
    }
}
