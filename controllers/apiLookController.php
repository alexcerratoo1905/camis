<?php
error_reporting(0);
session_start();

require_once "../config/db.php";
require_once "../models/look.php";

$idPrenda = isset($_GET['idPrenda']) ? intval($_GET['idPrenda']) : 0;
$idColor = isset($_GET['idColor']) ? intval($_GET['idColor']) : 0;

$db = new Database();
$conexion = $db->conectar();
$lookModel = new Look($conexion);

$respuesta = ['exito' => false, 'productos' => []];

if ($idPrenda > 0 && $idColor > 0) {
    $tieneLook = $lookModel->comprobarLook($idPrenda, $idColor);
    
    if ($tieneLook) {
        $respuesta['exito'] = true;
        $respuesta['productos'] = $lookModel->mostrarLook($idPrenda, $idColor);
    }
}

ob_clean();
header('Content-Type: application/json');
echo json_encode($respuesta);
exit;