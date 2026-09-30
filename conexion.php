<?php
$host = '127.0.0.1';
$baseDeDatos = 'workshop1';
$usuario = 'root';
$password = '';

try {
    $conexion = new PDO(
        "mysql:host=$host;dbname=$baseDeDatos;charset=utf8mb4",
        $usuario,
        $password
    );
} catch (PDOException $error) {
    exit('No se pudo conectar con MySQL. Importa database.sql y verifica que MySQL este activo.');
}
