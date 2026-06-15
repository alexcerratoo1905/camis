<?php
require_once '../config/db.php';
require_once '../models/usuario.php';

header('Content-Type: application/json');

if (isset($_GET['fecha'])) {
    $fecha = $_GET['fecha'];
    $db = new Database();
    $conn = $db->conectar();

    $sql = "SELECT DATE_FORMAT(fecha_cita, '%H:%i') as hora, COUNT(*) as total 
            FROM citas 
            WHERE DATE(fecha_cita) = :fecha AND estado != 'cancelada' 
            GROUP BY DATE_FORMAT(fecha_cita, '%H:%i') 
            HAVING total >= 10";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':fecha', $fecha);
    $stmt->execute();
    
    $horasLlenas = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $horasLlenas[] = $row['hora']; 
    }

    echo json_encode($horasLlenas);
} else {
    echo json_encode([]);
}
?>