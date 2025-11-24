<?php
header('Content-Type: application/json');

// Credenciales
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
$data = json_decode(file_get_contents("php://input"), true);

// Verificar si el JSON llegó bien y tiene el campo que queremos
if (!isset($data['nombre_bd']) || empty($data['nombre_bd'])) {
    die(json_encode([
        "success" => false, 
        "message" => "Error: No se recibió JSON válido o falta 'nombre_bd'."
    ]));
}


$dbName = $data['nombre_bd'];

// Create connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD);

// Check connection
if ($conn->connect_error) {
    $response["success"] = false;
    $response["message"] = "Fallo conexión al servidor: " . $conn->connect_error;
    echo json_encode($response);
    exit();
}

// Seleccionar la BD (Si falla, es que no creaste la BD con el script anterior)
if (!$conn->select_db($dbName)) {
    die(json_encode(["success" => false, "message" => "La base de datos '$dbName' no existe."]));
}

// 3. CREAR TABLA (Si no existe)
// Creamos: ID (auto), Username (texto), Score (entero), Fecha (automática)
$sqlTable = "CREATE TABLE IF NOT EXISTS jugadores (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    score INT(10) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sqlTable) !== TRUE) {
    die(json_encode(["success" => false, "message" => "Error creando tabla: " . $conn->error]));
}else{
    $response["success"] = true;
    $response["message"] = "Tabla creadas";
}

echo json_encode($response);

$conn->close();
?>