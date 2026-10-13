<?php
require 'conexion.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: registro.php');
    exit;
}
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$provinciaId = filter_input(INPUT_POST, 'provincia_id', FILTER_VALIDATE_INT);
if ($username === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6 || !$provinciaId) {
    header('Location: registro.php?error=1');
    exit;
}
try {
    $consulta = $conexion->prepare('INSERT INTO usuarios (username, email, password, provincia_id) VALUES (?, ?, ?, ?)');
    $consulta->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT), $provinciaId]);
    header('Location: index.php?registrado=1');
    exit;
} catch (PDOException $error) {
    if ($error->getCode() === '23000') {
        header('Location: registro.php?error=1');
        exit;
    }
    throw $error;
}
