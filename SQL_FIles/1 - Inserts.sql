INSERT INTO products (nombre, precio, stock) VALUES ('Monitor Curvo', 350.00, 20);

UPDATE products SET precio = 1250.00, stock = 40 WHERE nombre = 'Laptop Dell';

CALL CREATE_PRODUCT('Laptop Dell', 1500.00, 50);

UPDATE products SET precio = 1250.00, stock = 0 WHERE nombre = 'Laptop Dell';

CALL DELETE_PRODUCT('Laptop Dell');