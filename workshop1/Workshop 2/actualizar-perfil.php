<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}
require 'conexion.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: perfil.php');
    exit;
}
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$provinciaId = filter_input(INPUT_POST, 'provincia_id', FILTER_VALIDATE_INT);
if ($username === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$provinciaId || ($password !== '' && strlen($password) < 6)) {
    header('Location: perfil.php?error=1');
    exit;
}
try {
    if ($password !== '') {
        $consulta = $conexion->prepare('UPDATE usuarios SET username = ?, email = ?, provincia_id = ?, password = ? WHERE id = ?');
        $consulta->execute([$username, $email, $provinciaId, password_hash($password, PASSWORD_DEFAULT), $_SESSION['usuario_id']]);
    } else {
        $consulta = $conexion->prepare('UPDATE usuarios SET username = ?, email = ?, provincia_id = ? WHERE id = ?');
        $consulta->execute([$username, $email, $provinciaId, $_SESSION['usuario_id']]);
    }
    header('Location: perfil.php?guardado=1');
    exit;
} catch (PDOException $error) {
    if ($error->getCode() === '23000') {
        header('Location: perfil.php?error=1');
        exit;
    }
    throw $error;
}
