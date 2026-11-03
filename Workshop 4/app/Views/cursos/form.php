<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title><?= e($titulo) ?> | Workshop 4</title><link rel="stylesheet" href="style.css"></head>
<body class="dashboard-page">
<?php require __DIR__ . '/../partials/navegacion.php'; ?>
<main class="dashboard-card">
    <span class="brand-mark">CURSOS</span><h1><?= e($titulo) ?></h1>
    <p class="dashboard-copy">Todos los campos son obligatorios.</p>
    <?php require __DIR__ . '/../partials/errores.php'; ?>
    <form method="post" action="<?= e($action) ?>">
        <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
        <label for="nombre">Nombre</label>
        <input id="nombre" name="nombre" value="<?= e($curso['nombre']) ?>" maxlength="150" required>
        <label for="codigo">Código de Curso</label>
        <input id="codigo" name="codigo" value="<?= e($curso['codigo']) ?>" maxlength="30" pattern="[A-Za-z0-9_-]{1,30}" required>
        <label for="carrera_id">Id de la Carrera</label>
        <select id="carrera_id" name="carrera_id" required>
            <option value="">Selecciona una carrera</option>
            <?php foreach ($carreras as $carrera): ?><option value="<?= (int) $carrera['id'] ?>" <?= (int) $curso['carrera_id'] === (int) $carrera['id'] ? 'selected' : '' ?>><?= (int) $carrera['id'] ?> — <?= e($carrera['nombre']) ?></option><?php endforeach; ?>
        </select>
        <label for="creditos">Créditos</label>
        <input type="number" id="creditos" name="creditos" value="<?= e($curso['creditos']) ?>" min="1" max="30" step="1" required>
        <label for="descripcion">Descripción del Curso</label>
        <textarea id="descripcion" name="descripcion" rows="5" maxlength="5000" required><?= e($curso['descripcion']) ?></textarea>
        <button type="submit"><?= $id ? 'Guardar cambios' : 'Crear Curso' ?></button>
    </form>
    <a class="logout-link" href="<?= e(url('/cursos')) ?>">Cancelar y volver a Cursos</a>
</main>
</body>
</html>
