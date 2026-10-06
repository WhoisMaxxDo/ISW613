<?php
require 'conexion.php';

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    header('Location: index.php?error=1');
    exit;
}

$consulta = $conexion->prepare(
    'SELECT id FROM usuarios WHERE username = ? AND password = ? LIMIT 1'
);
$consulta->execute([$username, $password]);

if ($consulta->fetch()) {
    header('Location: usuario-existe.php');
    exit;
}

header('Location: index.php?error=1');
exit;
