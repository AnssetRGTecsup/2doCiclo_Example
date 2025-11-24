<?php

 header("Content-Type: application/json");

 define('DB_SERVER', 'localhost');
 define('DB_USERNAME', 'root');
 define('DB_PASSWORD', '');

 $response = array();

/*if (!isset($_POST['nombre_bd']) || empty($_POST['nombre_bd'])) {
    die(json_encode([
        "success" => false, 
        "message" => "Error: No se envió el nombre de la base de datos."
    ]));
}*/

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

$sql_database = "CREATE DATABASE IF NOT EXISTS " . $dbName;

if ($conn->query($sql_database) === TRUE) {
    $conn->select_db($dbName);
    
    $response["success"] = true;
    $response["message"] = "Conexión establecida y Base de Datos lista.";
    $response["db_name"] = $dbName;
} else {
    $response["success"] = false;
    $response["message"] = "Error creando la BD: " . $conn->error;
}

echo json_encode($response);
$conn->close();

?>
