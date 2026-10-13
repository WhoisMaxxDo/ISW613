<?php
$host = '127.0.0.1';
$baseDeDatos = 'workshop2';
$usuario = 'root';
$password = '';

try {
    $conexion = new PDO(
        "mysql:host=$host;dbname=$baseDeDatos;charset=utf8mb4",
        $usuario,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
} catch (PDOException $error) {
    exit('No se pudo conectar con MySQL. Importa database.sql y verifica que MySQL este activo.');
}
