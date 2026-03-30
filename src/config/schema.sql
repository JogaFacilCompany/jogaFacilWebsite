CREATE DATABASE IF NOT EXISTS jogafacil CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE jogafacil;

CREATE TABLE IF NOT EXISTS usuarios (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nome       VARCHAR(100)  NOT NULL,
    email      VARCHAR(150)  NOT NULL UNIQUE,
    senha      VARCHAR(255)  NOT NULL,
    tipo       ENUM('locador','locatario','gerente') NOT NULL DEFAULT 'locatario',
    cpf        VARCHAR(14)   DEFAULT NULL UNIQUE,
    criadoEm  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserindo usuários de teste (senha para ambos é: password)
INSERT INTO usuarios (nome, email, senha, tipo, cpf) VALUES
('Locador Admin', 'locador@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'locador', '11111111111'),
('Locatario Teste', 'locatario@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'locatario', '22222222222'),
('Gerente Admin', 'admin@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'gerente', '33333333333');
