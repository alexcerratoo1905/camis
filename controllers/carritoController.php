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
            $_SESSION['descuento'] = [
                'codigo' => $codigo,
                'porcentaje' => $datosCodigo['porcentaje_descuento']
            ];
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

    if (!isset($_POST['talla']) || empty($_POST['talla'])) {
        if ($origen === 'segundaMano') {
            header("Location: ../segundaMano.php?error=falta_talla");
        } else {
            header("Location: ../fichaProducto.php?idPrenda=" . $idPrenda . "&color=" . $color_id . "&error=falta_talla");
        }
        exit;
    }
    
    $talla = $_POST['talla'];

    $stmt = $conexion->prepare("SELECT stock FROM producto_tallas WHERE producto_id = ? AND color_id = ? AND talla = ?");
    $stmt->execute([$idPrenda, $color_id, $talla]);
    $resultadoStock = $stmt->fetch(PDO::FETCH_ASSOC);
    $stockMaximo = $resultadoStock ? $resultadoStock['stock'] : 0;

    if ($stockMaximo < 1) {
        if ($origen === 'segundaMano') {
            header("Location: ../segundaMano.php?error=no_stock");
        } else {
            header("Location: ../fichaProducto.php?idPrenda=" . $idPrenda . "&color=" . $color_id . "&error=no_stock");
        }
        exit;
    }

    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    $productoEncontrado = false;
    foreach ($_SESSION['carrito'] as &$item) {
        if ($item['idPrenda'] == $idPrenda && $item['talla'] == $talla && $item['color_id'] == $color_id) {
            
            if ($item['cantidad'] + $cantidad > $stockMaximo) {
                if ($origen === 'segundaMano') {
                    header("Location: ../segundaMano.php?error=no_stock");
                } else {
                    header("Location: ../fichaProducto.php?idPrenda=" . $idPrenda . "&color=" . $color_id . "&error=no_stock");
                }
                exit;
            }
            
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
            'cantidad' => $cantidad
        ];
    }

    if ($origen === 'segundaMano') {
        header("Location: ../segundaMano.php?mensaje=agregado");
    } else {
        header("Location: ../fichaProducto.php?idPrenda=" . $idPrenda . "&color=" . $color_id );
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['accion']) && isset($_GET['indice'])) {
    $indice = (int)$_GET['indice'];

    if (isset($_SESSION['carrito'][$indice])) {
        if ($_GET['accion'] == 'sumar') {
            
            $item = $_SESSION['carrito'][$indice];
            
            $stmt = $conexion->prepare("SELECT stock FROM producto_tallas WHERE producto_id = ? AND color_id = ? AND talla = ?");
            $stmt->execute([$item['idPrenda'], $item['color_id'], $item['talla']]);
            $resultadoStock = $stmt->fetch(PDO::FETCH_ASSOC);
            $stockMaximo = $resultadoStock ? $resultadoStock['stock'] : 0;

            if ($item['cantidad'] < $stockMaximo) {
                $_SESSION['carrito'][$indice]['cantidad']++;
            } else {
                header("Location: ../carrito.php?error=no_stock");
                exit;
            }

        } elseif ($_GET['accion'] == 'restar') {
            $_SESSION['carrito'][$indice]['cantidad']--;
            if ($_SESSION['carrito'][$indice]['cantidad'] <= 0) {
                unset($_SESSION['carrito'][$indice]);
            }
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
        if($cp['id'] == $item['color_id']) {
            $nombreColor = $cp['nombre'];
            break;
        }
    }

    $rebaja = isset($datosProd['rebaja']) ? (int)$datosProd['rebaja'] : 0;
    $precioUnitario = $datosProd['precio'] - ($datosProd['precio'] * $rebaja / 100);

    $subtotal = $precioUnitario * $item['cantidad'];
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
        'subtotal' => $subtotal
    ];
}
?>