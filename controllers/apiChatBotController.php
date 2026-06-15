<?php
session_start();
require_once "../config/db.php";
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $json = file_get_contents('php://input');
    $datos = json_decode($json, true);

    if (isset($datos['mensaje']) && !empty($datos['mensaje'])) {
        $mensajeUsuario = trim($datos['mensaje']);
        
        try {
            $db = new Database();
            $conexion = $db->conectar(); 
            
            $usuarioId = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;
            $sessionId = session_id(); 
            
            $sql = "INSERT INTO chat_logs (usuario_id, session_id, mensaje) VALUES (:user_id, :session_id, :mensaje)";
            $sentencia = $conexion->prepare($sql);
            $sentencia->execute([
                ':user_id' => $usuarioId,
                ':session_id' => $sessionId,
                ':mensaje' => $mensajeUsuario
            ]);
        } catch (Exception $e) {
            error_log("Error guardando log de chat: " . $e->getMessage());
        }

        $datosN8n = [
            "mensaje" => $mensajeUsuario,
            "sessionId" => session_id()
        ];

        $config = parse_ini_file(__DIR__ . '/../config/config.ini');
        $urlWebhook = $config['base_url'] . $config['chatbot'];

        $curl = curl_init($urlWebhook);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($datosN8n));
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60); 

        $respuesta_n8n = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($http_code === 200) {
            $respuestaDecodificada = json_decode($respuesta_n8n, true);
            $textoRespuesta = "Error leyendo la consulta.";

            if (isset($respuestaDecodificada['output'])) {
                $textoRespuesta = $respuestaDecodificada['output'];
            } else if (isset($respuestaDecodificada['text'])) {
                $textoRespuesta = $respuestaDecodificada['text'];
            }

            echo json_encode(["status" => "success", "respuesta" => $textoRespuesta]);
        } else {
            echo json_encode(["status" => "error", "message" => "Mis circuitos están saturados."]);
        }
    }
}