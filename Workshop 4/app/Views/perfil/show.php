<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi perfil | Workshop 4</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="dashboard-page">
<?php require __DIR__ . '/../partials/navegacion.php'; ?>
<main class="dashboard-card">
    <span class="brand-mark">W4</span><p class="eyebrow">PERFIL DE USUARIO</p><h1>Hola, <?= e($usuario['username']) ?>.</h1>
    <p class="dashboard-copy">Actualiza los datos asociados a tu cuenta.</p>
    <?php if ($guardado): ?><div class="success" role="status">Tus datos se actualizaron correctamente.</div><?php endif; ?>
    <?php require __DIR__ . '/../partials/errores.php'; ?>
    <form method="post" action="<?= e(url('/perfil')) ?>">
        <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
        <label for="username">
            Nombre de usuario
        </label>
        <input class="form-input" type="text" id="username" name="username" value="<?= e($usuario['username']) ?>" maxlength="100" required>
        <label for="email">
            Correo electrónico
        </label>
        <input class="form-input" type="email" id="email" name="email" value="<?= e($usuario['email']) ?>" maxlength="150" required>
        <label for="provincia_id">
            Provincia
        </label>
        <select class="form-input" id="provincia_id" name="provincia_id" required>
            <?php foreach ($provincias as $provincia): ?>
            <option value="<?= (int) $provincia['id'] ?>" <?= (int) $provincia['id'] === (int) $usuario['provincia_id'] ? 'selected' : '' ?>><?= e($provincia['nombre']) ?></option><?php endforeach; ?>
        </select>
        <label for="password">
            Nueva contraseña <span class="muted">(opcional)</span>
        </label>
        <input class="form-input" type="password" id="password" name="password" minlength="6" autocomplete="new-password" placeholder="Déjala en blanco para conservarla">
        <button type="submit">Guardar cambios</button>
    </form>
    <p class="dashboard-copy">Provincia actual: <strong><?= e($usuario['provincia']) ?></strong></p>
    <a class="logout-link" href="<?= e(url('/logout')) ?>">Cerrar sesión</a>
</main>
</body>
</html>
