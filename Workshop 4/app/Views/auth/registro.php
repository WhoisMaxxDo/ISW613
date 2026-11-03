<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Registro | Workshop 4</title><link rel="stylesheet" href="style.css"></head>
<body class="login-page">
<main class="login-card">
    <section class="login-intro"><p class="eyebrow">WORKSHOP 4</p><h1>Crea tu cuenta.</h1><p class="intro-text">Completa tus datos y selecciona tu provincia.</p></section>
    <section class="login-form-section">
        <div class="form-heading"><p class="eyebrow">NUEVA CUENTA</p><h2>Registro</h2><p>Todos los campos son obligatorios.</p></div>
        <?php require __DIR__ . '/../partials/errores.php'; ?>
        <form method="post" action="<?= e(url('/registro')) ?>">
            <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
            <label for="username">Nombre de usuario</label><input class="form-input" type="text" id="username" name="username" value="<?= e($usuario['username']) ?>" maxlength="100" autocomplete="username" required>
            <label for="email">Correo electrónico</label><input class="form-input" type="email" id="email" name="email" value="<?= e($usuario['email']) ?>" maxlength="150" autocomplete="email" required>
            <label for="password">Contraseña</label><input class="form-input" type="password" id="password" name="password" minlength="6" autocomplete="new-password" required>
            <label for="provincia_id">Provincia</label>
            <select class="form-input" id="provincia_id" name="provincia_id" required><option value="">Selecciona una provincia</option><?php foreach ($provincias as $provincia): ?><option value="<?= (int) $provincia['id'] ?>" <?= (int) $usuario['provincia_id'] === (int) $provincia['id'] ? 'selected' : '' ?>><?= e($provincia['nombre']) ?></option><?php endforeach; ?></select>
            <button type="submit">Crear cuenta</button>
        </form>
        <p class="demo-hint">¿Ya tienes cuenta? <a href="<?= e(url('/')) ?>">Inicia sesión</a>.</p>
    </section>
</main>
</body>
</html>
