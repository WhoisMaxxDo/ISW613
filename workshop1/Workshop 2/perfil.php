<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}
require 'conexion.php';
$consulta = $conexion->prepare('SELECT u.id, u.username, u.email, u.provincia_id, p.nombre AS provincia FROM usuarios u JOIN provincias p ON p.id = u.provincia_id WHERE u.id = ?');
$consulta->execute([$_SESSION['usuario_id']]);
$usuario = $consulta->fetch();
if (!$usuario) {
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit;
}
$provincias = $conexion->query('SELECT id, nombre FROM provincias ORDER BY nombre')->fetchAll();
$guardado = ($_GET['guardado'] ?? '') === '1';
$error = ($_GET['error'] ?? '') === '1';
function e($valor) { return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Mi perfil | Workshop 2</title><link rel="stylesheet" href="style.css"></head>
<body class="dashboard-page">
<main class="dashboard-card">
    <span class="brand-mark">W2</span><p class="eyebrow">PERFIL DE USUARIO</p><h1>Hola, <?= e($usuario['username']) ?>.</h1>
    <p class="dashboard-copy">Actualiza los datos asociados a tu cuenta.</p>
    <?php if ($guardado): ?><div class="success" role="status">Tus datos se actualizaron correctamente.</div><?php endif; ?>
    <?php if ($error): ?><div class="alert" role="alert">No se pudieron actualizar los datos. Verifica que el correo no esté en uso.</div><?php endif; ?>
    <form method="post" action="actualizar-perfil.php">
        <label for="username">Nombre de usuario</label><input class="form-input" type="text" id="username" name="username" value="<?= e($usuario['username']) ?>" maxlength="100" required>
        <label for="email">Correo electrónico</label><input class="form-input" type="email" id="email" name="email" value="<?= e($usuario['email']) ?>" maxlength="150" required>
        <label for="provincia_id">Provincia</label><select class="form-input" id="provincia_id" name="provincia_id" required><?php foreach ($provincias as $provincia): ?><option value="<?= (int) $provincia['id'] ?>" <?= (int) $provincia['id'] === (int) $usuario['provincia_id'] ? 'selected' : '' ?>><?= e($provincia['nombre']) ?></option><?php endforeach; ?></select>
        <label for="password">Nueva contraseña <span class="muted">(opcional)</span></label><input class="form-input" type="password" id="password" name="password" minlength="6" autocomplete="new-password" placeholder="Déjala en blanco para conservarla">
        <button type="submit">Guardar cambios</button>
    </form>
    <p class="dashboard-copy">Provincia actual: <strong><?= e($usuario['provincia']) ?></strong></p>
    <a class="logout-link" href="logout.php">Cerrar sesión</a>
</main>
</body>
</html>
