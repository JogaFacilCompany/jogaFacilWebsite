-- Joga Fácil – Database Schema
-- Run this via phpMyAdmin or MySQL Workbench

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

-- =============================================
-- QUADRAS (cadastradas pelo locador)
-- =============================================
CREATE TABLE IF NOT EXISTS quadras (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    locadorId     INT            NOT NULL,
    nome          VARCHAR(100)   NOT NULL,
    endereco      VARCHAR(255)   NOT NULL,
    cnpj          VARCHAR(18)    DEFAULT NULL UNIQUE,
    descricao     TEXT           DEFAULT NULL,
    precoHora     DECIMAL(10,2)  NOT NULL,
    imagemUrl     VARCHAR(500)   DEFAULT NULL,
    ativo         BOOLEAN        NOT NULL DEFAULT TRUE,
    criadoEm      TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    atualizadoEm  TIMESTAMP      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_quadras_locador
        FOREIGN KEY (locadorId) REFERENCES usuarios(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,

    CONSTRAINT uq_quadras_nome_locador
        UNIQUE (locadorId, nome)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_quadras_ativo ON quadras(ativo);

-- =============================================
-- HORARIOS DISPONIVEIS (janelas recorrentes por dia da semana)
-- =============================================
CREATE TABLE IF NOT EXISTS horarios_disponiveis (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    quadraId      INT            NOT NULL,
    diaSemana     TINYINT        NOT NULL COMMENT '0=domingo, 6=sabado',
    horaInicio    TIME           NOT NULL,
    horaFim       TIME           NOT NULL,
    preco         DECIMAL(10,2)  DEFAULT NULL COMMENT 'Override do precoHora da quadra; NULL = usar padrao',
    criadoEm      TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_horarios_quadra
        FOREIGN KEY (quadraId) REFERENCES quadras(id)
        ON DELETE CASCADE ON UPDATE CASCADE,

    CONSTRAINT chk_horarios_periodo
        CHECK (horaFim > horaInicio),

    CONSTRAINT chk_horarios_dia_semana
        CHECK (diaSemana BETWEEN 0 AND 6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_horarios_quadra_dia ON horarios_disponiveis(quadraId, diaSemana);

-- =============================================
-- RESERVAS (solicitações de locatários)
-- =============================================
CREATE TABLE IF NOT EXISTS reservas (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    quadraId        INT            NOT NULL,
    locatarioId     INT            NOT NULL,
    dataReserva     DATE           NOT NULL,
    horaInicio      TIME           NOT NULL,
    horaFim         TIME           NOT NULL,
    status          ENUM('pendente','confirmada','recusada','cancelada')
                                   NOT NULL DEFAULT 'pendente',
    motivoRecusa    VARCHAR(255)   DEFAULT NULL,
    criadoEm        TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    atualizadoEm    TIMESTAMP      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_reservas_quadra
        FOREIGN KEY (quadraId) REFERENCES quadras(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,

    CONSTRAINT fk_reservas_locatario
        FOREIGN KEY (locatarioId) REFERENCES usuarios(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,

    CONSTRAINT chk_reservas_periodo
        CHECK (horaFim > horaInicio),

    CONSTRAINT uq_reservas_slot
        UNIQUE (quadraId, dataReserva, horaInicio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_reservas_locatario ON reservas(locatarioId);
CREATE INDEX idx_reservas_status ON reservas(status);
CREATE INDEX idx_reservas_data ON reservas(quadraId, dataReserva);

-- =============================================
-- DADOS DE TESTE
-- =============================================

-- Inserindo usuários de teste (senha para ambos é: password)
INSERT INTO usuarios (nome, email, senha, tipo, cpf) VALUES
('Locador Admin', 'locador@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'locador', '11111111111'),
('Locatario Teste', 'locatario@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'locatario', '22222222222'),
('Gerente Admin', 'admin@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'gerente', '33333333333');

-- Quadra de teste (vinculada ao Locador Admin, id=1)
INSERT INTO quadras (locadorId, nome, endereco, cnpj, descricao, precoHora, imagemUrl) VALUES
(1, 'Arena Central', 'Rua das Flores, 123 - Centro', '12.345.678/0001-90', 'Quadra poliesportiva coberta com vestiários', 150.00, NULL);

-- Horários disponíveis para a quadra de teste (seg a sex)
INSERT INTO horarios_disponiveis (quadraId, diaSemana, horaInicio, horaFim, preco) VALUES
(1, 1, '08:00', '11:00', NULL),
(1, 1, '13:00', '17:00', 180.00),
(1, 1, '19:00', '22:00', 200.00),
(1, 2, '08:00', '11:00', NULL),
(1, 2, '13:00', '17:00', 180.00),
(1, 2, '19:00', '22:00', 200.00),
(1, 3, '08:00', '11:00', NULL),
(1, 3, '13:00', '17:00', 180.00),
(1, 3, '19:00', '22:00', 200.00),
(1, 4, '08:00', '11:00', NULL),
(1, 4, '13:00', '17:00', 180.00),
(1, 4, '19:00', '22:00', 200.00),
(1, 5, '08:00', '11:00', NULL),
(1, 5, '13:00', '17:00', 180.00),
(1, 5, '19:00', '22:00', 200.00);
