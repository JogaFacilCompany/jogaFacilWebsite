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
    Inativo_motivo VARCHAR(500) DEFAULT NULL,
    foto_perfil VARCHAR(255)  DEFAULT NULL,
    created_at   TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
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
    UNIQUE KEY unique_horario_quadra_data_inicio (quadra_id, data, hora_inicio),
    FOREIGN KEY (quadra_id) REFERENCES quadras(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS reservas (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    horario_id  INT NOT NULL,
    quadra_id   INT NOT NULL,
    usuario_id  INT NOT NULL,
    status              ENUM('pendente', 'confirmada', 'cancelada') NOT NULL DEFAULT 'pendente',
    modo_lobby          BOOLEAN DEFAULT FALSE,
    visibilidade_lobby  ENUM('publico', 'privado') DEFAULT NULL,
    codigo_acesso       VARCHAR(20) DEFAULT NULL,
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (horario_id) REFERENCES horarios(id) ON DELETE CASCADE,
    FOREIGN KEY (quadra_id) REFERENCES quadras(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS lobby_participantes (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    reserva_id  INT NOT NULL,
    usuario_id  INT NOT NULL,
    joined_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_participante_lobby (reserva_id, usuario_id),
    FOREIGN KEY (reserva_id) REFERENCES reservas(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS gerente_quadras (
    gerente_id INT NOT NULL,
    quadra_id  INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (gerente_id, quadra_id),
    FOREIGN KEY (gerente_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (quadra_id) REFERENCES quadras(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS quadra_imagens (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    quadra_id   INT NOT NULL,
    nome_arquivo VARCHAR(255) NOT NULL,
    ordem       INT DEFAULT 0,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (quadra_id) REFERENCES quadras(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS notificacoes (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id  INT NOT NULL,
    mensagem    VARCHAR(500) NOT NULL,
    link        VARCHAR(255) DEFAULT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserindo usuários de teste (senha para todos é: password)
INSERT INTO usuarios (nome, email, senha, tipo, cpf) VALUES
('Locador Teste', 'locador@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'locador', '11111111111'),
('Locatario Teste', 'locatario@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'locatario', '22222222222'),
('Admin', 'admin@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '33333333333');

-- Mockups para interface
INSERT INTO quadras (nome, endereco, imagem, locador_id, status, descricao, facilidades, telefone) VALUES
('Arena Gol de Placa', 'Rua do Ouro, 123 - Centro', NULL, 1, 'ativo', 'Excelente quadra de society para o seu rachão', '["Cantina", "Vestiários", "Aluguel de Bola", "Bebedouro"]', '(11) 99999-9999');
