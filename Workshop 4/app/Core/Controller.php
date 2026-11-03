<?php
namespace App\Core;

abstract class Controller {
    protected function render(string $view, array $data = []): void {
        $data += ['csrf' => $_SESSION['csrf']];
        extract($data, EXTR_SKIP);
        require __DIR__ . '/../Views/' . $view . '.php';
    }

    protected function redirect(string $route, array $parameters = []): void {
        header('Location: ' . url($route, $parameters), true, 303);
        exit;
    }

    protected function authenticate(): int {
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('/');
        }
        return (int) $_SESSION['usuario_id'];
    }

    protected function verifyCsrf(): void {
        if (!is_string($_POST['csrf'] ?? null) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
            $this->error(403, 'Solicitud inválida. Vuelve al formulario e inténtalo nuevamente.');
            exit;
        }
    }

    public function error(int $status, string $message): void {
        http_response_code($status);
        $this->render('errors/http', ['status' => $status, 'message' => $message]);
    }

    protected function text(string $field): string {
        return is_string($_POST[$field] ?? null) ? trim($_POST[$field]) : '';
    }
}
