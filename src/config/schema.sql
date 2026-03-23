 - Joga Fácil – Database Schema
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
