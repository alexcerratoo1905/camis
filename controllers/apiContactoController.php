<?php
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $nombre = $data['nombre'] ?? '';
    $email = $data['email'] ?? '';
    $mensaje = $data['mensaje'] ?? '';

    if (empty($nombre) || empty($email) || empty($mensaje)) {
        echo json_encode(['exito' => false, 'error' => 'Faltan datos']);
        exit;
    }

    $config = parse_ini_file(__DIR__ . '/../config/config.ini');
    $webhook = $config['base_url'] . $config['contacto'];

    $postData = json_encode([
        'nombre' => $nombre,
        'email' => $email,
        'mensaje' => $mensaje,
        'fecha' => date('d/m/Y H:i:s')
    ]);

    $ch = curl_init($webhook);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($postData)
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode >= 200 && $httpCode < 300) {
        echo json_encode(['exito' => true]);
    } else {
        echo json_encode(['exito' => false, 'error' => 'Fallo la conexión con n8n']);
    }
} else {
    echo json_encode(['exito' => false, 'error' => 'Método no permitido']);
}