<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/producto.php';
require_once __DIR__ . '/../models/imagen.php';
require_once __DIR__ . '/../models/usuario.php'; 

$db = new Database();
$conexion = $db->conectar();
$productoModel = new Producto($conexion);
$imagenModel = new Imagen($conexion);
$usuarioModel = new Usuario($conexion);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accion']) && $_POST['accion'] == 'aplicar_descuento') {
    $codigo = strtoupper(trim($_POST['codigo_descuento']));
    if (isset($_SESSION['usuario_id'])) {
        $datosUsuario = $usuarioModel->obtenerDatosUsu($_SESSION['usuario_id']);
        $emailUsuario = trim($datosUsuario['email']);
        $datosCodigo = $productoModel->verificarCodigoDescuento($codigo, $emailUsuario); 
        if ($datosCodigo) {
            $_SESSION['descuento'] = ['codigo' => $codigo, 'porcentaje' => $datosCodigo['porcentaje_descuento']];
            header("Location: ../carrito.php?mensaje=codigo_aplicado");
        } else {
            header("Location: ../carrito.php?error=codigo_invalido");
        }
    } else {
        header("Location: ../carrito.php?error=no_sesion");
    }
    exit;
}

if (isset($_GET['accion']) && $_GET['accion'] == 'quitar_descuento') {
    unset($_SESSION['descuento']);
    header("Location: ../carrito.php?mensaje=codigo_quitado");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accion']) && $_POST['accion'] == 'agregar') {
    
    $idPrenda = $_POST['idPrenda'];
    $color_id = isset($_POST['color_id']) ? $_POST['color_id'] : (isset($_POST['color']) ? $_POST['color'] : '');
    $cantidad = 1;
    $origen = isset($_POST['origen']) ? $_POST['origen'] : 'ficha';

    // Recogemos todos los extras del dropshipping
    $talla = $_POST['talla'] ?? '';
    $extra_player = isset($_POST['extra_player']) ? 1 : 0;
    $extra_pantalon = isset($_POST['extra_pantalon']) ? 1 : 0;
    $tiene_parche = isset($_POST['tiene_parche']) ? 1 : 0;
    $texto_parche = $tiene_parche ? trim($_POST['texto_parche']) : '';
    $tiene_personalizacion = isset($_POST['tiene_personalizacion']) ? 1 : 0;
    $texto_nombre = $tiene_personalizacion ? trim($_POST['texto_nombre']) : '';
    $texto_numero = $tiene_personalizacion ? trim($_POST['texto_numero']) : '';

    if (empty($talla)) {
        header("Location: ../fichaProducto.php?idPrenda=" . $idPrenda . "&color=" . $color_id . "&error=falta_talla");
        exit;
    }

    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    $productoEncontrado = false;
    foreach ($_SESSION['carrito'] as &$item) {
        // Agrupamos en la misma línea del carrito SÓLO si todo (talla, color y todos los extras) es exactamente igual
        if ($item['idPrenda'] == $idPrenda && $item['talla'] == $talla && $item['color_id'] == $color_id &&
            ($item['extra_player'] ?? 0) == $extra_player &&
            ($item['extra_pantalon'] ?? 0) == $extra_pantalon &&
            ($item['tiene_parche'] ?? 0) == $tiene_parche &&
            ($item['texto_parche'] ?? '') == $texto_parche &&
            ($item['tiene_personalizacion'] ?? 0) == $tiene_personalizacion &&
            ($item['texto_nombre'] ?? '') == $texto_nombre &&
            ($item['texto_numero'] ?? '') == $texto_numero
        ) {
            $item['cantidad'] += $cantidad;
            $productoEncontrado = true;
            break;
        }
    }

    if (!$productoEncontrado) {
        $_SESSION['carrito'][] = [
            'idPrenda' => $idPrenda,
            'talla' => $talla,
            'color_id' => $color_id,
            'cantidad' => $cantidad,
            'extra_player' => $extra_player,
            'extra_pantalon' => $extra_pantalon,
            'tiene_parche' => $tiene_parche,
            'texto_parche' => $texto_parche,
            'tiene_personalizacion' => $tiene_personalizacion,
            'texto_nombre' => $texto_nombre,
            'texto_numero' => $texto_numero
        ];
    }

    header("Location: ../fichaProducto.php?idPrenda=" . $idPrenda . "&color=" . $color_id );
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['accion']) && isset($_GET['indice'])) {
    $indice = (int)$_GET['indice'];

    if (isset($_SESSION['carrito'][$indice])) {
        // Ya no hay bloqueo de stock
        if ($_GET['accion'] == 'sumar') {
            $_SESSION['carrito'][$indice]['cantidad']++;
        } elseif ($_GET['accion'] == 'restar') {
            $_SESSION['carrito'][$indice]['cantidad']--;
            if ($_SESSION['carrito'][$indice]['cantidad'] <= 0) unset($_SESSION['carrito'][$indice]);
        } elseif ($_GET['accion'] == 'eliminar') {
            unset($_SESSION['carrito'][$indice]);
        }
        $_SESSION['carrito'] = array_values($_SESSION['carrito']);
    }

    header("Location: ../carrito.php");
    exit;
}

$carritoActual = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];
$carritoDetallado = [];
$totalCarrito = 0;

foreach ($carritoActual as $indice => $item) {
    $datosProd = $productoModel->obtenerProducto($item['idPrenda']);
    $imagenesColor = $imagenModel->listarImagenesPorColor($item['idPrenda'], $item['color_id']);
    $foto = !empty($imagenesColor) ? $imagenesColor[0]['url_imagen'] : 'public/img/fondo.jpg';
    
    $coloresProducto = $productoModel->obtenerColoresPorProducto($item['idPrenda']);
    $nombreColor = "Color";
    foreach($coloresProducto as $cp) {
        if($cp['id'] == $item['color_id']) { $nombreColor = $cp['nombre']; break; }
    }

    $rebaja = isset($datosProd['rebaja']) ? (int)$datosProd['rebaja'] : 0;
    $precioBaseRebajado = $datosProd['precio'] - ($datosProd['precio'] * $rebaja / 100);

    // Sumar los costes extra al precio unitario real que pagará el cliente
    $extraPrecio = 0;
    if (!empty($item['extra_player'])) $extraPrecio += 3;
    if (!empty($item['extra_pantalon'])) $extraPrecio += 10;
    if (!empty($item['tiene_parche'])) $extraPrecio += 1;
    if (!empty($item['tiene_personalizacion'])) $extraPrecio += 2;
    if (in_array($item['talla'], ['2XL', '3XL', '4XL'])) $extraPrecio += 1;

    $precioUnitarioFinal = $precioBaseRebajado + $extraPrecio;
    $subtotal = $precioUnitarioFinal * $item['cantidad'];
    $totalCarrito += $subtotal;

    $carritoDetallado[] = [
        'indice' => $indice, 
        'idPrenda' => $item['idPrenda'],
        'color_id' => $item['color_id'],
        'nombre' => $datosProd['nombre'],
        'precio_original' => $datosProd['precio'], 
        'rebaja' => $rebaja, 
        'talla' => $item['talla'],
        'color_nombre' => $nombreColor,
        'cantidad' => $item['cantidad'],
        'imagen' => $foto,
        'subtotal' => $subtotal,
        // Pasamos también los extras a la vista para pintarlos en el Checkout si quieres luego
        'extra_player' => $item['extra_player'] ?? 0,
        'extra_pantalon' => $item['extra_pantalon'] ?? 0,
        'tiene_parche' => $item['tiene_parche'] ?? 0,
        'texto_parche' => $item['texto_parche'] ?? '',
        'tiene_personalizacion' => $item['tiene_personalizacion'] ?? 0,
        'texto_nombre' => $item['texto_nombre'] ?? '',
        'texto_numero' => $item['texto_numero'] ?? ''
    ];
}
?>