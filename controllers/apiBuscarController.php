<?php


require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/producto.php";



$db = new Database();
$producto = new Producto($db->conectar());

$prendaABuscar = isset($_GET["q"]) ? $_GET["q"] : "";

header('Content-Type: application/json');

$apiKey = isset($_GET['apiKey']) ? $_GET['apiKey'] : '';
if ($apiKey !== 'Z5qbS7OXmHrLOoxa8BXWEDxjh5P5qffBQHCH9aV2mDrWtXxTcKsFssJvwFc4Todc') {
    http_response_code(401);
    echo json_encode(["error" => "Acceso denegado. Se requiere API Key válida."]);
    exit;
}

if(empty($prendaABuscar)){
    echo json_encode([]);
    exit;
}

$resultado = $producto->buscarPorNombreChatBot($prendaABuscar);

echo json_encode($resultado);

?>