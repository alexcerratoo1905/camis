<?php
require_once '../config/db.php';
require_once '../models/producto.php'; 

header('Content-Type: application/json');

if (isset($_GET['idPrenda']) && isset($_GET['idColor'])) {
    
    $idPrenda = $_GET['idPrenda'];
    $idColor = $_GET['idColor'];

    $db = new Database();

    try {
    
        $productoObj = new Producto($db ->conectar());
        
        $todasLasTallas = $productoObj->obtenerTallas($idPrenda);
        
        $tallasDelColor = [];
        foreach ($todasLasTallas as $talla) {
            if ($talla['color_id'] == $idColor) {
                $tallasDelColor[] = [
                    "talla" => $talla['talla'],
                    "stock" => $talla['stock']
                ];
            }
        }

        echo json_encode($tallasDelColor);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Fallo al obtener las tallas"]);
    }
    
    exit;
}

echo json_encode([]);