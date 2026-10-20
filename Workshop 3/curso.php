<?php
require 'sesion.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (isset($_GET['id']) && !$id) {
    http_response_code(400);
    exit('Identificador de curso inválido.');
}
$curso = ['nombre' => '', 'codigo' => '', 'carrera_id' => '', 'creditos' => '', 'descripcion' => ''];
if ($id) {
    $consulta = $conexion->prepare('SELECT * FROM cursos WHERE id = ?');
    $consulta->execute([$id]);
    $curso = $consulta->fetch();
    if (!$curso) {
        header('Location: cursos.php?estado=no-encontrado');
        exit;
    }
}
$carreras = $conexion->query('SELECT id, nombre FROM carreras ORDER BY nombre')->fetchAll();
$errores = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificarCsrf();
    foreach (['nombre', 'codigo', 'descripcion'] as $campo) {
        $curso[$campo] = is_string($_POST[$campo] ?? null) ? trim($_POST[$campo]) : '';
    }
    $curso['carrera_id'] = filter_input(INPUT_POST, 'carrera_id', FILTER_VALIDATE_INT);
    $curso['creditos'] = filter_input(INPUT_POST, 'creditos', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 30]]);
    if ($curso['nombre'] === '' || mb_strlen($curso['nombre']) > 150) {
        $errores[] = 'El nombre es obligatorio y debe tener hasta 150 caracteres.';
    }
    if (!preg_match('/^[A-Za-z0-9_-]{1,30}$/', $curso['codigo'])) {
        $errores[] = 'El código debe tener hasta 30 caracteres: letras, números, guiones o guiones bajos.';
    }
    if (!in_array($curso['carrera_id'], array_column($carreras, 'id'))) {
        $errores[] = 'Selecciona una carrera válida.';
    }
    if (!$curso['creditos']) {
        $errores[] = 'Los créditos deben ser un número entero entre 1 y 30.';
    }
    if ($curso['descripcion'] === '' || mb_strlen($curso['descripcion']) > 5000) {
        $errores[] = 'La descripción es obligatoria y debe tener hasta 5000 caracteres.';
    }
    if (!$errores) {
        try {
            $datos = [$curso['nombre'], $curso['codigo'], $curso['carrera_id'], $curso['creditos'], $curso['descripcion']];
            if ($id) {
                $datos[] = $id;
                $consulta = $conexion->prepare('UPDATE cursos SET nombre = ?, codigo = ?, carrera_id = ?, creditos = ?, descripcion = ? WHERE id = ?');
            } else {
                $consulta = $conexion->prepare('INSERT INTO cursos (nombre, codigo, carrera_id, creditos, descripcion) VALUES (?, ?, ?, ?, ?)');
            }
            $consulta->execute($datos);
            header('Location: cursos.php?estado=' . ($id ? 'editado' : 'creado'), true, 303);
            exit;
        } catch (PDOException $error) {
            $errores[] = $error->getCode() === '23000' ? 'El código del curso ya está registrado o la carrera ya no existe.' : 'No se pudo guardar el curso. Inténtalo nuevamente.';
        }
    }
}
$titulo = $id ? 'Editar Curso' : 'Crear Curso';
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title><?= e($titulo) ?> | Workshop 3</title><link rel="stylesheet" href="style.css"></head>
<body class="dashboard-page">
<?php require 'navegacion.php'; ?>
<main class="dashboard-card">
    <span class="brand-mark">CURSOS</span><h1><?= e($titulo) ?></h1>
    <p class="dashboard-copy">Todos los campos son obligatorios.</p>
    <?php if ($errores): ?><div class="alert" role="alert"><ul><?php foreach ($errores as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <form method="post" action="curso.php<?= $id ? '?id=' . (int) $id : '' ?>">
        <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
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
    <a class="logout-link" href="cursos.php">Cancelar y volver a Cursos</a>
</main>
</body>
</html>
