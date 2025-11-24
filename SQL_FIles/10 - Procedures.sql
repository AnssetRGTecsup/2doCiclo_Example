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

