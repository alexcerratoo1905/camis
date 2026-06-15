<?php
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $json = file_get_contents('php://input');
    $datos = json_decode($json, true);

    if (isset($datos['altura']) && isset($datos['peso'])) {
        
        $config = parse_ini_file(__DIR__ . '/../config/config.ini');
        $urlWebhook = $config['base_url'] . $config['asistenteTallas'];



        $curl = curl_init($urlWebhook);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($datos));
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 15); 
        
        $respuestaN8n = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($http_code === 200) {
            echo trim($respuestaN8n);
        } else {
            echo json_encode(["status" => "error", "message" => "N8N no responde."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Faltan datos."]);
    }
    exit;
}
?>