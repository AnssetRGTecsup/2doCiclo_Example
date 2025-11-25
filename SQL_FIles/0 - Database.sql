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

DELIMITER //

CREATE PROCEDURE CREATE_PRODUCT(
    IN in_product_name VARCHAR(255),
    IN in_product_price DECIMAL(10,2),
    IN in_product_stock INT
)
BEGIN
	INSERT INTO products (nombre, precio, stock)
    VALUES (in_product_name, in_product_price, in_product_stock);
END//

DELIMITER ;

DELIMITER //

CREATE PROCEDURE DELETE_PRODUCT(
    IN in_product_name VARCHAR(255)
)
BEGIN
	DECLARE stock_product INT;
    
    SELECT stock INTO stock_product FROM products
    WHERE nombre = in_product_name;
    
	IF stock_product = 0 THEN
    	DELETE FROM products WHERE nombre = in_product_name;
    END IF;
END//

DELIMITER ;

DELIMITER //

CREATE TRIGGER after_product_insert
AFTER INSERT ON products FOR EACH ROW
BEGIN
    INSERT INTO product_audit (
        product_id,
        product_nombre,
        product_precio,
        product_stock,
        audit_message
    )
    VALUES (
        NEW.id,
        NEW.nombre,
        NEW.precio,
        NEW.stock,
        'Nuevo producto'
    );
END//

DELIMITER ;

DELIMITER //

CREATE TRIGGER after_product_update
AFTER UPDATE ON products FOR EACH ROW
BEGIN
    -- Verificar si el precio o el stock han cambiado
    IF OLD.precio <> NEW.precio OR OLD.stock <> NEW.stock THEN
        INSERT INTO product_audit (
            product_id,
            product_nombre,
            product_precio,
            product_stock,
            audit_message
        )
        VALUES (
            NEW.id,
            NEW.nombre,
            NEW.precio,
            NEW.stock,
            'Producto actualizado'
        );
    END IF;
END//

DELIMITER ;

DELIMITER //

CREATE TRIGGER before_product_delete
BEFORE DELETE ON products FOR EACH ROW
BEGIN
    INSERT INTO product_audit (
        product_id,
        product_nombre,
        product_precio,
        product_stock,
        audit_message
    )
    VALUES (
        OLD.id,
        OLD.nombre,
        OLD.precio,
        OLD.stock,
        'Producto eliminado'
    );
END//

DELIMITER ;