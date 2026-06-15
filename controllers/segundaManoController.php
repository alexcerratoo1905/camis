<?php

session_start();

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/producto.php";
require_once __DIR__ . "/../models/imagen.php";

$db = new Database();
$producto = new Producto($db->conectar());

$listaTallas = $producto->listarTodasTallas();
$listaColores = $producto->listaColores();
$listaTipoPrenda = $producto->listarTiposPrendas();

$idUsuarioActivo = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;

$catalogoSegundaMano = $producto->obtenerCatalogoSegundaMano($idUsuarioActivo); 

?>