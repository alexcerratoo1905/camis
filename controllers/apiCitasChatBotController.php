<?php

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/cita.php";

header('Content-Type: application/json');

$apiKey = isset($_GET['apiKey']) ? $_GET['apiKey'] : '';
if ($apiKey !== 'Z5qbS7OXmHrLOoxa8BXWEDxjh5P5qffBQHCH9aV2mDrWtXxTcKsFssJvwFc4Todc') {
    http_response_code(401);
    echo json_encode(["error" => "Acceso denegado. Se requiere API Key válida."]);
    exit;
}

if (isset($_GET['fecha']) && !empty(trim($_GET['fecha']))) {
    $fecha = trim($_GET['fecha']);
    
    $hora = (isset($_GET['hora']) && trim($_GET['hora']) !== "") ? trim($_GET['hora']) : null;
    
    $db = new Database();
    $cita = new Cita($db->conectar()); 
    
    $horasLibres = $cita->buscarHorasLibresChatBot($fecha, $hora);
    
    if (count($horasLibres) > 0) {
        if ($hora !== null) {
            $mensaje = "¡Genial! Tenemos hueco disponible a las $hora el día $fecha.";
        } else {
            $listaHoras = array_column($horasLibres, 'hora');
            $mensaje = "Horas libres para el $fecha: " . implode(', ', $listaHoras);
        }
    } else {
        if ($hora !== null) {
            $mensaje = "Lo siento, el aforo a las $hora el día $fecha está completo o la hora no es válida.";
        } else {
            $mensaje = "Lo siento, la agenda está completamente llena el $fecha.";
        }
    }

    echo json_encode(["estado_citas" => $mensaje]);

} else {
    echo json_encode(["error" => "Falta el parámetro fecha."]);
}
?>