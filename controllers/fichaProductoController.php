<?php

session_start();



require_once "./models/producto.php";
require_once "./models/imagen.php";
require_once "./config/db.php";
require_once __DIR__ . '/../models/favorito.php';
require_once __DIR__ . '/../models/look.php';

$db = new Database();
$producto = new Producto($db->conectar());
$imagen = new Imagen($db->conectar());
$look = new Look($db->conectar());

$idPrenda = isset($_GET["idPrenda"]) ? $_GET["idPrenda"] : 0;
$datosPrenda = $producto->obtenerProducto($idPrenda);

if (!$datosPrenda) {
    header("Location: catalogo.php");
    exit;
}

$galeria = $imagen->listarImagenes($idPrenda);
$listaTallas = $producto->obtenerTallas($idPrenda);
$tallasJson = json_encode($listaTallas);
$coloresProducto = $producto->obtenerColoresPorProducto($idPrenda);

$colorPorDefecto = !empty($coloresProducto) ? $coloresProducto['id'] : 0;

$idColorActual = isset($_GET['color']) ? $_GET['color'] : $colorPorDefecto;

$arrayFavoritos = [];

if (isset($_SESSION['usuario_id'])) {
    $favoritoModel = new Favorito($db->conectar());
    $misFavoritos = $favoritoModel->listarFavoritos($_SESSION['usuario_id']);

    foreach ($misFavoritos as $fav) {
        $arrayFavoritos[] = $fav['id'] . '-' . $fav['color_id'];
    }
}


$productosLook = [];
$tieneLook = false;

if ($idColorActual > 0) {
    $tieneLook = $look->comprobarLook($idPrenda, $idColorActual);

    if ($tieneLook) {
        $productosLook =  $look->mostrarLook($idPrenda, $idColorActual);
    }
}
