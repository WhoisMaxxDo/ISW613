<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    header('Location: perfil.php');
    exit;
}
$error = ($_GET['error'] ?? '') === '1';
$registrado = ($_GET['registrado'] ?? '') === '1';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión | Workshop 2</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">
    <main class="login-card">
        <section class="login-intro">
            <p class="eyebrow">WORKSHOP 2</p>
            <h1>Bienvenido de nuevo.</h1>
            <p class="intro-text">Inicia sesión para consultar y actualizar tu perfil.</p>
        </section>
        <section class="login-form-section">
            <div class="form-heading"><p class="eyebrow">CUENTA</p><h2>Iniciar sesión</h2><p>Ingresa tus datos para continuar.</p></div>
            <?php if ($error): ?><div class="alert" role="alert">Usuario o contraseña incorrectos.</div><?php endif; ?>
            <?php if ($registrado): ?><div class="success" role="status">Cuenta creada. Ya puedes iniciar sesión.</div><?php endif; ?>
            <form method="post" action="login.php">
                <label for="username">Nombre de usuario</label>
                <input class="form-input" type="text" id="username" name="username" autocomplete="username" required autofocus>
                <label for="password">Contraseña</label>
                <input class="form-input" type="password" id="password" name="password" autocomplete="current-password" required>
                <button type="submit">Entrar</button>
            </form>
            <p class="demo-hint">¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>.</p>
        </section>
    </main>
</body>
</html>
