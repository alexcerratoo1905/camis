<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/usuario.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $json = file_get_contents('php://input');
    $datos = json_decode($json, true);

    if (isset($datos['email']) && !empty($datos['email'])) {
        $email = trim($datos['email']);
        
        try {
            $db = new Database();
            $conexion = $db->conectar();
            $usu = new Usuario($conexion);

            $codigoUnico = $usu->generarCodigoDescuento();

            $usu -> crearCodigo($codigoUnico,$email);

        $config = parse_ini_file(__DIR__ . '/../config/config.ini');
        $urlWebhook = $config['base_url'] . $config['solicitarCodigo'];

            $curl = curl_init($urlWebhook);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode([
                "email" => $email,
                "codigo" => $codigoUnico 
            ]));
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            
            $respuesta = curl_exec($curl);
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if ($http_code === 200) {
                echo json_encode(["status" => "success"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Fallo de comunicación con n8n."]);
            }

        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => "Error DB: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Falta el email."]);
    }
    exit;
}
?>