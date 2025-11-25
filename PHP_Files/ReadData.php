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

    $nombre_a_buscar = $data['Name'];
    //$nombre_a_buscar = 'Monitor Curvo';

    

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
    $sql = "SELECT id, nombre, precio, stock FROM products WHERE nombre = ?";

    // Usar Prepared Statements para seguridad
    $stmt = $conn->prepare($sql);

    // Verificar si la preparación falló
    if ($stmt === false) {
        $response["message"] = "Error al preparar la consulta: " . $conn->error;
    } else {
        // 5. Asignar parámetros y ejecutar
        $stmt->bind_param("s", $nombre_a_buscar);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // 6. Procesar resultados
        if ($result->num_rows > 0) {
            // Se encontró el producto (solo uno, gracias a UNIQUE)
            $row = $result->fetch_assoc();
            
            // Mapear al formato ProductsDB (todo como string)
            $product_db = [
                "ID"    => (string) $row['id'],
                "Name"  => (string) $row['nombre'],
                "Price" => (string) $row['precio'],
                "Stock" => (string) $row['stock']
            ];
            
            // Llenar el objeto de respuesta ProductsResponse
            $response["success"] = true;
            $response["message"] = "Producto encontrado con éxito.";
            $response["products"][] = $product_db; // Lo agregamos al array 'products'
            
        } else {
            // No se encontró el producto
            $response["success"] = true; // La consulta se ejecutó correctamente (no hubo error de BD)
            $response["message"] = "Producto no encontrado: " . $nombre_a_buscar;
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