<?php

session_start();

require_once "models/producto.php";
require_once "models/imagen.php";
require_once "config/db.php";
require_once __DIR__ . '/../models/favorito.php';

$db = new DataBase();
$producto = new Producto($db->conectar());
$imagen = new Imagen($db->conectar());

$esModoSecreto = (isset($_GET['especial']) && $_GET['especial'] == 'herror');

if ($esModoSecreto) {
    if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['acceso']) || $_SESSION['acceso'] !== true) {
        header("Location: index.php?error=acceso_denegado");
        exit();
    }
}

function crearUrl($clave, $valor) {
    $parametros = $_GET;
    $parametros[$clave] = $valor;
    if (isset($parametros['pagina'])) {
        unset($parametros['pagina']);
    }
    return '?' . http_build_query($parametros);
}

$ordenActual = isset($_GET["orden"]) ? $_GET["orden"] : null;

$filtrosActivos = array_filter($_GET, function($key) {
    return in_array($key, ['genero', 'coleccion', 'tipo', 'talla', 'color', 'rebajas', 'precioMin', 'precioMax', 'orden']);
}, ARRAY_FILTER_USE_KEY);

if (!empty($filtrosActivos)) {
    $listaProductos = $producto->filtrarCombinado($_GET, $esModoSecreto);
    
    $titulos = [];
    if(isset($_GET['genero'])) $titulos[] = ($_GET['genero'] == 1 ? "Hombre" : ($_GET['genero'] == 2 ? "Mujer" : "Unisex"));
    if(isset($_GET['tipo'])) {
        $datosTiposPrendas = $producto->obtenerTipoPrenda($_GET["tipo"]);
        $titulos[] = $datosTiposPrendas['nombre'];
    }
    if(isset($_GET['color'])) $titulos[] = "Color " . $_GET['color'];
    if(isset($_GET['talla'])) $titulos[] = "Talla " . $_GET['talla'];
    if(isset($_GET['rebajas'])) $titulos[] = "Rebajas";
    
    $mensajeFiltrado = !empty($titulos) ? implode(" | ", $titulos) : "Resultados de búsqueda";
} else {
    if ($esModoSecreto) {
        $listaProductos = $producto->obtenerColeccionSecreta(); 
        $mensajeFiltrado = "Colección Exclusiva";
    } else {
        $listaProductos = $producto->listarProductos(1);
        $mensajeFiltrado = "Todos los productos";
    }
}

if ($esModoSecreto) {
    $listaCategorias = []; 
    $listaColores = $producto->obtenerColoresColeccionSecreta();
} else {
    $listaCategorias = $producto->listarColecciones();
    $listaColores = $producto->listaColores();
}

$listaTiposProductos = $producto->listarTiposPrendas();

$precioMax = $producto->obtenerPrecioMinMax("MAX", $esModoSecreto);
$precioMin = $producto->obtenerPrecioMinMax("MIN", $esModoSecreto);

$arrayFavoritos = [];
if (isset($_SESSION['usuario_id'])) {
    $favoritoModel = new Favorito($db->conectar());
    $misFavoritos = $favoritoModel->listarFavoritos($_SESSION['usuario_id']);
    
    foreach ($misFavoritos as $fav) {
        $arrayFavoritos[] = $fav['id'] . '-' . $fav['color_id'];
    }
}

?>