<?php

session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["exito" => false, "mensaje" => "noLogin"]);
    exit;
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/favorito.php';

$idUsu = $_SESSION["usuario_id"];
$idPrenda = isset($_POST["idPrenda"]) ? $_POST["idPrenda"] : 0;
$colorPrenda = isset($_POST["idColor"]) ? $_POST["idColor"] : 0;

if($idPrenda <= 0 || $colorPrenda <= 0){
        echo json_encode(["exito" => false, "mensaje" => "id_invalido"]);
    exit;
}

$db = new Database();
$favorito = new favorito($db->conectar());

if($favorito->esFavorito($idUsu, $idPrenda, $colorPrenda)){
    $favorito -> eliminarFavoritos($idUsu, $idPrenda, $colorPrenda);
    echo json_encode(["exito" => true, "accion" => "eliminado"]);
}else{
    $favorito -> agregarFavorito($idUsu, $idPrenda, $colorPrenda);
    echo json_encode(["exito" => true, "accion" => "agregado"]);
}





?>