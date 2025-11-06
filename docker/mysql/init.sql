CREATE DATABASE IF NOT EXISTS bienes_raices;
USE bienes_raices;

CREATE TABLE IF NOT EXISTS vendedores (
    id_ven INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    nombre VARCHAR(45),
    apellido VARCHAR(45),
    telefono VARCHAR(10)
);

CREATE TABLE IF NOT EXISTS propiedades (
    id_prop INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    titulo VARCHAR(45),
    precio DECIMAL(10,2),
    imagen VARCHAR(200),
    descripcion LONGTEXT,
    habitaciones INT,
    wc INT,
    estacionamiento INT,
    creado DATE,
    id_ven INT,
    CONSTRAINT id_ven FOREIGN KEY (id_ven) REFERENCES vendedores(id_ven)
);

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    email VARCHAR(50),
    password CHAR(60)
);
