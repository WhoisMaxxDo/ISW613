<?php
require 'conexion.php';
$provincias = $conexion->query('SELECT id, nombre FROM provincias ORDER BY nombre')->fetchAll();
$error = ($_GET['error'] ?? '') === '1';
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Registro | Workshop 2</title><link rel="stylesheet" href="style.css"></head>
<body class="login-page">
<main class="login-card">
    <section class="login-intro"><p class="eyebrow">WORKSHOP 2</p><h1>Crea tu cuenta.</h1><p class="intro-text">Completa tus datos y selecciona tu provincia.</p></section>
    <section class="login-form-section">
        <div class="form-heading"><p class="eyebrow">NUEVA CUENTA</p><h2>Registro</h2><p>Todos los campos son obligatorios.</p></div>
        <?php if ($error): ?><div class="alert" role="alert">No se pudo crear la cuenta. Revisa los datos; el usuario o correo podrían estar registrados.</div><?php endif; ?>
        <form method="post" action="guardar-registro.php">
            <label for="username">Nombre de usuario</label><input class="form-input" type="text" id="username" name="username" maxlength="100" autocomplete="username" required>
            <label for="email">Correo electrónico</label><input class="form-input" type="email" id="email" name="email" maxlength="150" autocomplete="email" required>
            <label for="password">Contraseña</label><input class="form-input" type="password" id="password" name="password" minlength="6" autocomplete="new-password" required>
            <label for="provincia_id">Provincia</label>
            <select class="form-input" id="provincia_id" name="provincia_id" required><option value="">Selecciona una provincia</option><?php foreach ($provincias as $provincia): ?><option value="<?= (int) $provincia['id'] ?>"><?= htmlspecialchars($provincia['nombre'], ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></select>
            <button type="submit">Crear cuenta</button>
        </form>
        <p class="demo-hint">¿Ya tienes cuenta? <a href="index.php">Inicia sesión</a>.</p>
    </section>
</main>
</body>
</html>
