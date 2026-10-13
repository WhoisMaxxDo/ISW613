CREATE DATABASE IF NOT EXISTS workshop2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE workshop2;

CREATE TABLE IF NOT EXISTS provincias (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
);

INSERT INTO provincias (nombre) VALUES
('San José'), ('Alajuela'), ('Cartago'), ('Heredia'),
('Guanacaste'), ('Puntarenas'), ('Limón')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

CREATE TABLE IF NOT EXISTS usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    provincia_id INT UNSIGNED NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuarios_provincias FOREIGN KEY (provincia_id) REFERENCES provincias(id)
);

-- Cuenta de ejemplo: usuario isw613, contraseña isw613.
INSERT INTO usuarios (username, email, password, provincia_id)
SELECT 'isw613', 'admin@workshop2.com', 'isw613', id FROM provincias WHERE nombre = 'San José'
ON DUPLICATE KEY UPDATE username = VALUES(username), password = VALUES(password), provincia_id = VALUES(provincia_id);
