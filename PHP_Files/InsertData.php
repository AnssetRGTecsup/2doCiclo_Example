<?php
    header('Content-Type: application/json');

    // Credenciales
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', '');
    define('DB_DATAbasE', 'tf_example');

    // Inicializamos el objeto final de respuesta ProductsResponse
    $response = [
        "success" => false,
        "message" => "Ocurrió un error desconocido.",
        "products" => []
    ];

    $data = json_decode(file_get_contents("php://input"), true);

    $pName = $data['Name'];
    $pPrice = $data['Price'];
    $pStock = $data['Stock'];

    
    // Create connection
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATAbasE);

    // Verificar la conexión
    if ($conn->connect_error) {
        $response["message"] = "Error de conexión a la BD: " . $conn->connect_error;
    
        // Devolvemos la respuesta de error y terminamos la ejecución
        header('Content-Type: application/json');
        die(json_encode($response));
    }

    // 4. Preparar la consulta SQL (SELECT * FROM products WHERE nombre = ?)
    //$sql = "SELECT id, nombre, precio, stock FROM products WHERE nombre = ?";
    //$sql = "INSERT INTO products (nombre, precio, stock) VALUES (?,?,?)";
    $sql = "CALL CREATE_PRODUCT(?,?,?)";

    // Usar Prepared Statements para seguridad
    $stmt = $conn->prepare($sql);

    // Verificar si la preparación falló
    if ($stmt === false) {
        $response["message"] = "Error al preparar la consulta: " . $conn->error;
    } else {
        // 5. Asignar parámetros y ejecutar
        $stmt->bind_param("sss", $pName, $pPrice, $pStock);
        if ($stmt->execute()) {
            // Éxito en la inserción
            $response["success"] = true;
            $response["message"] = "Producto '{$pName}' insertado correctamente.";

        } else {
            // Error de ejecución (ej: nombre duplicado si es UNIQUE)
            $response["message"] = "Error al insertar el producto: " . $stmt->error;
        }

        // 7. Cerrar statement
        $stmt->close();
    }

    // 8. Cerrar conexión a la BD
    $conn->close();

    // 9. Configurar cabecera y devolver el JSON final
    header('Content-Type: application/json');
    echo json_encode($response);
?>