<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/look.php";

header('Content-Type: application/json');

$apiKey = isset($_GET['apiKey']) ? $_GET['apiKey'] : '';
if ($apiKey !== 'Z5qbS7OXmHrLOoxa8BXWEDxjh5P5qffBQHCH9aV2mDrWtXxTcKsFssJvwFc4Todc') {
    http_response_code(401);
    echo json_encode(["error" => "Acceso denegado. Se requiere API Key válida."]);
    exit;
}

$db = new Database();
$look = new Look($db->conectar());

$resultados = $look->obtenerTodosLosLooks();

if (count($resultados) > 0) {
    echo json_encode(["looks_disponibles" => $resultados]);
} else {
    echo json_encode(["mensaje" => "Actualmente no tenemos looks predefinidos publicados."]);
}
?>