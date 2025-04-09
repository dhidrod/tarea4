<?php
/*
-- Creación de la base de datos
CREATE DATABASE IF NOT EXISTS tarea4recuperacion2;
USE tarea4recuperacion2;

-- Tabla usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido1 VARCHAR(100) NOT NULL,
    apellido2 VARCHAR(100),
    nick VARCHAR(50) NOT NULL UNIQUE,
    correo VARCHAR(100) NOT NULL UNIQUE,
    fecha_nacimiento DATE NOT NULL,
    password VARCHAR(255) NOT NULL,
    saldo DECIMAL(10,2) NOT NULL DEFAULT 0.00
);

-- Tabla salas
CREATE TABLE salas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    capacidad INT NOT NULL
);

-- Tabla asientos
CREATE TABLE asientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sala_id INT NOT NULL,
    posicion VARCHAR(10) NOT NULL,
    precio DECIMAL(6,2) NOT NULL,
    disponible BOOLEAN NOT NULL DEFAULT TRUE,
    FOREIGN KEY (sala_id) REFERENCES salas(id) ON DELETE CASCADE,
    UNIQUE KEY (sala_id, posicion)
);

-- Tabla entradas
CREATE TABLE entradas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    asiento_id INT NOT NULL,
    fecha_compra DATE NOT NULL,
    precio_compra DECIMAL(6,2) NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (asiento_id) REFERENCES asientos(id) ON DELETE CASCADE
);

-- Tabla cuenta_cine
CREATE TABLE cuenta_cine (
    id INT AUTO_INCREMENT PRIMARY KEY,
    saldo DECIMAL(10,2) NOT NULL DEFAULT 0.00
);

-- Insertamos un registro inicial en cuenta_cine
INSERT INTO cuenta_cine (saldo) VALUES (0.00);

-- Índices adicionales para mejorar el rendimiento
CREATE INDEX idx_asientos_sala ON asientos(sala_id);
CREATE INDEX idx_entradas_usuario ON entradas(usuario_id);
CREATE INDEX idx_entradas_asiento ON entradas(asiento_id);


-- Ejemplos

-- Usuarios

INSERT INTO usuarios (nombre, apellido1, apellido2, nick, correo, fecha_nacimiento, password, saldo) VALUES
('Juan', 'García', 'López', 'juangl', 'juan.garcia@example.com', '1990-05-15', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 100.00),
('María', 'Rodríguez', 'Martínez', 'mariaRod', 'maria.rodriguez@example.com', '1985-12-03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 75.50),
('Pedro', 'González', 'Sánchez', 'pedroGS', 'pedro.gonzalez@example.com', '1992-07-22', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 120.00),
('Ana', 'Fernández', 'Pérez', 'anaFP', 'ana.fernandez@example.com', '1988-09-10', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 50.25),
('Carlos', 'López', 'Gómez', 'carlosLG', 'carlos.lopez@example.com', '1993-01-30', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 200.00),
('Laura', 'Martínez', 'Díaz', 'lauraMD', 'laura.martinez@example.com', '1995-11-18', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 85.75),
('David', 'Sánchez', 'Vázquez', 'davidSV', 'david.sanchez@example.com', '1991-04-25', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 150.50),
('Elena', 'Gómez', 'Torres', 'elenaGT', 'elena.gomez@example.com', '1987-08-14', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 65.30),
('Miguel', 'Pérez', 'Ruiz', 'miguelPR', 'miguel.perez@example.com', '1994-02-08', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 180.25),
('Carmen', 'Torres', 'García', 'carmenTG', 'carmen.torres@example.com', '1989-06-20', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 90.00),
('Daniel', 'Hidalgo', 'Rodríguez', 'daniHR', 'daniel@example.com', '1993-07-26', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 500.00);

-- Salas

INSERT INTO salas (nombre, capacidad) VALUES
('Sala Principal', 100),
('Sala VIP', 50),
('Sala 3D', 80),
('Sala Pequeña', 40),
('Sala Mediana', 60);

-- Asientos
INSERT INTO `asientos` (`id`, `sala_id`, `posicion`, `precio`, `disponible`) VALUES
(NULL, '1', '1', '5', '1'),
(NULL, '1', '2', '5', '1'),
(NULL, '1', '3', '5', '1'),
(NULL, '1', '4', '5', '1'),
(NULL, '1', '5', '5', '1'),
(NULL, '1', '6', '5', '1'),
(NULL, '1', '7', '5', '1'),
(NULL, '1', '8', '5', '1'),
(NULL, '1', '9', '5', '1'),
(NULL, '1', '10', '5', '1'),
(NULL, '1', '11', '5', '1'),
(NULL, '1', '12', '5', '1'),
(NULL, '1', '13', '5', '1'),
(NULL, '1', '14', '5', '1'),
(NULL, '1', '15', '5', '1'),
(NULL, '1', '16', '5', '1'),
(NULL, '1', '17', '5', '1'),
(NULL, '1', '18', '5', '1'),
(NULL, '1', '19', '5', '1'),
(NULL, '1', '20', '5', '1');



*/