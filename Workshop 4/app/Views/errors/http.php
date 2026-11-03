<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Error <?= (int) $status ?> | Workshop 4</title><link rel="stylesheet" href="style.css"></head>
<body class="login-page"><main class="dashboard-card">
    <h1>Error <?= (int) $status ?></h1><p><?= e($message) ?></p>
    <a class="logout-link" href="<?= e(url('/')) ?>">Volver al inicio</a>
</main></body>
</html>
