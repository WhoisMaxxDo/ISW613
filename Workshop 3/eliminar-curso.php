<?php
require 'sesion.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit('Método no permitido. Eliminar un curso requiere POST.');
}
verificarCsrf();
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$id) {
    http_response_code(400);
    exit('Identificador de curso inválido.');
}
$consulta = $conexion->prepare('DELETE FROM cursos WHERE id = ?');
$consulta->execute([$id]);
header('Location: cursos.php?estado=' . ($consulta->rowCount() ? 'eliminado' : 'no-encontrado'), true, 303);
exit;
