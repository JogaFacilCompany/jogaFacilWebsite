CREATE DATABASE IF NOT EXISTS jogafacil CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE jogafacil;

CREATE TABLE IF NOT EXISTS usuarios (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nome       VARCHAR(100)  NOT NULL,
    email      VARCHAR(150)  NOT NULL UNIQUE,
    senha      VARCHAR(255)  NOT NULL,
    tipo       ENUM('locador','locatario','gerente','admin') NOT NULL DEFAULT 'locatario',
    cpf        VARCHAR(14)   DEFAULT NULL UNIQUE,
    status     ENUM('ativo', 'inativo') NOT NULL DEFAULT 'ativo',
    criadoEm   TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS quadras (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nome        VARCHAR(100) NOT NULL,
    endereco    VARCHAR(255) NOT NULL,
    imagem      VARCHAR(255) DEFAULT NULL,
    locador_id  INT NOT NULL,
    cnpj        VARCHAR(14) DEFAULT NULL,
    status      ENUM('pendente', 'ativo', 'inativo', 'manutencao', 'rejeitado') NOT NULL DEFAULT 'pendente',
    descricao   TEXT DEFAULT NULL,
    facilidades TEXT DEFAULT NULL,
    modalidades VARCHAR(255) DEFAULT 'Futebol',
    funcionamento VARCHAR(100) DEFAULT '08:00 - 23:00',
    cancelamento_horas INT DEFAULT 24,
    telefone    VARCHAR(20) DEFAULT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (locador_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS horarios (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    quadra_id   INT NOT NULL,
    data        DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fim    TIME NOT NULL,
    preco       DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    FOREIGN KEY (quadra_id) REFERENCES quadras(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS reservas (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    horario_id  INT NOT NULL,
    quadra_id   INT NOT NULL,
    usuario_id  INT NOT NULL,
    status      ENUM('pendente', 'confirmada', 'cancelada') NOT NULL DEFAULT 'pendente',
    modo_lobby  BOOLEAN DEFAULT FALSE,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (horario_id) REFERENCES horarios(id) ON DELETE CASCADE,
    FOREIGN KEY (quadra_id) REFERENCES quadras(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserindo usuários de teste (senha para ambos é: password)
INSERT INTO usuarios (nome, email, senha, tipo, cpf) VALUES
('Locador Admin', 'locador@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'locador', '11111111111'),
('Locatario Teste', 'locatario@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'locatario', '22222222222'),
('Gerente Admin', 'admin@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '33333333333');

-- Mockups para interface
INSERT INTO quadras (nome, endereco, imagem, locador_id, status, descricao, facilidades, telefone) VALUES
('Arena Gol de Placa', 'Rua do Ouro, 123 - Centro', 'https://images.unsplash.com/photo-1543351611-58f69d7c1781?auto=format&fit=crop&q=80&w=1000', 1, 'ativo', 'Excelente quadra de society para o seu rachão', '["Cantina", "Vestiários", "Aluguel de Bola", "Bebedouro"]', '(11) 99999-9999');
