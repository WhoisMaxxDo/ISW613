<?php
require 'sesion.php';
$cursos = $conexion->query('SELECT c.*, ca.nombre AS carrera FROM cursos c JOIN carreras ca ON ca.id = c.carrera_id ORDER BY c.nombre')->fetchAll();
$mensajes = ['creado' => 'Curso creado correctamente.', 'editado' => 'Curso actualizado correctamente.', 'eliminado' => 'Curso eliminado correctamente.', 'no-encontrado' => 'El curso solicitado ya no existe.'];
$mensaje = $mensajes[$_GET['estado'] ?? ''] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cursos | Workshop 3</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="dashboard-page">
<?php require 'navegacion.php'; ?>
<main class="dashboard-card courses-card">
    <div class="section-heading">
        <div><span class="brand-mark">OFERTA ACADÉMICA</span><h1>Cursos</h1><p class="dashboard-copy">Consulta y administra los cursos disponibles.</p></div>
        <a class="button-link" href="curso.php">Crear Curso</a>
    </div>
    <?php if ($mensaje): ?><div class="success" role="status"><?= e($mensaje) ?></div><?php endif; ?>
    <div class="table-scroll">
        <table>
            <caption>Listado de cursos</caption>
            <thead><tr><th scope="col">Nombre</th><th scope="col">Código de Curso</th><th scope="col">Id de la Carrera</th><th scope="col">Créditos</th><th scope="col">Descripción del Curso</th><th scope="col">Acciones</th></tr></thead>
            <tbody>
            <?php foreach ($cursos as $curso): ?>
                <tr>
                    <td><?= e($curso['nombre']) ?></td>
                    <td><?= e($curso['codigo']) ?></td>
                    <td><?= (int) $curso['carrera_id'] ?> — <?= e($curso['carrera']) ?></td>
                    <td><?= (int) $curso['creditos'] ?></td>
                    <td class="description-cell"><?= e($curso['descripcion']) ?></td>
                    <td><div class="course-actions">
                        <a class="button-link secondary" href="curso.php?id=<?= (int) $curso['id'] ?>" aria-label="Editar <?= e($curso['nombre']) ?>">Editar</a>
                        <form method="post" action="eliminar-curso.php" onsubmit="return confirm('¿Deseas eliminar este curso?');">
                            <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
                            <input type="hidden" name="id" value="<?= (int) $curso['id'] ?>">
                            <button class="danger" type="submit" aria-label="Eliminar <?= e($curso['nombre']) ?>">Eliminar</button>
                        </form>
                    </div></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$cursos): ?><tr><td colspan="6">No hay cursos registrados. Usa «Crear Curso» para agregar el primero.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
</body>
</html>
