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