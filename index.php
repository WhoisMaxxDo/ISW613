<?php
session_start();

if (isset($_SESSION['usuario'])) {
    header('Location: dashboard.php');
    exit;
}

require 'conexion.php';

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Completa todos los campos.';
    } else {
        $consulta = $conexion->prepare('SELECT id, username, email, password FROM usuarios WHERE username = ? LIMIT 1');
        $consulta->execute([$username]);
        $usuario = $consulta->fetch();

        if ($usuario && $password === $usuario['password']) {
            $_SESSION['usuario'] = [
                'id' => $usuario['id'],
                'username' => $usuario['username'],
                'email' => $usuario['email']
            ];

            header('Location: dashboard.php');
            exit;
        }

        $error = 'El correo o la contraseña no son correctos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesion | Workshop 1</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">
    <main class="login-card">
        <section class="login-intro">
            <p class="eyebrow">WORKSHOP 1</p>
            <h1>Bienvenido de nuevo.</h1>
            <p class="intro-text">Entregable 1 de ISW613.</p>
        </section>

        <section class="login-form-section">
            <div class="form-heading">
                <p class="eyebrow">CUENTA</p>
                <h2>Iniciar sesion</h2>
                <p>Ingresa tus datos para continuar.</p>
            </div>

            <?php if ($error !== ''): ?>
                <div class="alert" role="alert"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post" action="">
                <label for="username">Nombre de usuario</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($username) ?>" placeholder="example: joomiiii" required autofocus>

                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="Ingresa tu contraseña" required>

                <button type="submit">Entrar</button>
            </form>
        </section>
    </main>
</body>
</html>
