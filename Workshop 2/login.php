<?php
session_start();
require 'conexion.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$consulta = $conexion->prepare('SELECT id, username, email, password FROM usuarios WHERE username = ? LIMIT 1');
$consulta->execute([$username]);
$usuario = $consulta->fetch();
$valida = $usuario && (password_verify($password, $usuario['password']) || hash_equals($usuario['password'], $password));
if (!$valida) {
    header('Location: index.php?error=1');
    exit;
}
if (password_needs_rehash($usuario['password'], PASSWORD_DEFAULT)) {
    $actualiza = $conexion->prepare('UPDATE usuarios SET password = ? WHERE id = ?');
    $actualiza->execute([password_hash($password, PASSWORD_DEFAULT), $usuario['id']]);
}
session_regenerate_id(true);
$_SESSION['usuario_id'] = (int) $usuario['id'];
header('Location: perfil.php');
exit;
