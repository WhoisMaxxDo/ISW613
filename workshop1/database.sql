CREATE DATABASE IF NOT EXISTS workshop1 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE workshop1;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO usuarios (username, email, password)
VALUES (
    'isw613',
    'admin@workshop1.com',
    'isw613'
)
ON DUPLICATE KEY UPDATE username = VALUES(username), password = VALUES(password);
