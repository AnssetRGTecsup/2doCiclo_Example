CREATE DATABASE TF_EXAMPLE;

USE TF_EXAMPLE;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL UNIQUE,
    precio DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL
);

CREATE TABLE product_audit (
    audit_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    product_nombre VARCHAR(255),
    product_precio DECIMAL(10, 2),
    product_stock INT,
    audit_message VARCHAR(255),
    audit_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);