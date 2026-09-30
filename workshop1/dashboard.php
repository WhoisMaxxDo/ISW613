<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}

$usuario = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel | Workshop 1</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="dashboard-page">
    <main class="dashboard-card">
        <span class="brand-mark">W1</span>
        <p class="eyebrow">PANEL PRINCIPAL</p>
        <h1>Hola, <?= htmlspecialchars($usuario['username']) ?>.</h1>
        <p class="dashboard-copy">Has iniciado sesion correctamente, tu email es: <strong><?= htmlspecialchars($usuario['email']) ?></strong>.</p>
        <a class="logout-link" href="logout.php">Cerrar sesion</a>
    </main>
</body>
</html>
