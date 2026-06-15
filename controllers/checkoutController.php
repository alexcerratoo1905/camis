<?php

session_start();

require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/usuario.php";
require_once __DIR__ . "/../models/producto.php";

if(!isset($_SESSION["carrito"]) || empty($_SESSION["carrito"])){
    header("Location: ../carrito.php");
    exit;

}


$db = new Database();
$user = new Usuario($db->conectar());
$productoModel = new Producto($db->conectar());

$idComprador = $_SESSION["usuario_id"];


$datosComprador = $user->obtenerDatosUsu($idComprador);




?>