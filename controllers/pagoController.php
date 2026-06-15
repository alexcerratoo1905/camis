<?php
session_start();
require_once '../config/db.php';
require_once '../models/pedido.php';
require_once '../models/producto.php';
require_once '../models/usuario.php';
require_once '../vendor/autoload.php';

if (!isset($_SESSION['usuario_id']) || empty($_SESSION['carrito'])) {
    header("Location: ../carrito.php");
    exit();
}

\Stripe\Stripe::setApiKey('sk_test_51TRSRfHJPlhS3OiOmWvQ9M4K1TuNPsHDsBNsV9l99ziXgumDDGjjQtGNQNprptcmSqS0QYrdrGx4AMaOr2HAcy5o006E97tSH6');

$db = new Database();
$conexion = $db->conectar();
$pedidoObj = new Pedido($conexion);
$productoObj = new Producto($conexion);
$idUsuario = $_SESSION['usuario_id'];
$usuario = new Usuario($conexion);

if (isset($_GET['status']) && $_GET['status'] == 'success') {

    $total = $_SESSION['checkout_data']['total'] ?? 0;
    $direccion = $_SESSION['checkout_data']['direccion'] ?? 'Dirección no proporcionada';

    $idPedido = $pedidoObj->crearPedido($idUsuario, $total, $direccion);

    if ($idPedido) {
        foreach ($_SESSION['carrito'] as $item) {
            $idProducto = $item['idPrenda'];
            $idColor    = $item['color_id'];
            $talla      = $item['talla'];
            $cantidad   = $item['cantidad'];

            $pedidoObj->crearDetallesPedidos($idPedido, $idProducto, $idColor, $talla, $cantidad);
            $productoObj->actualizarStock($idProducto, $idColor, $talla, $cantidad);
        }


            if (isset($_SESSION['descuento']['codigo'])) {
            $usuario->marcarCodigoUsado($_SESSION['descuento']['codigo']);
            unset($_SESSION['descuento']); 
        }

        unset($_SESSION['carrito']);
        unset($_SESSION['checkout_data']);



        header("Location: ../gracias.php");
        exit();
    } else {
        die("Error al procesar el pedido en la base de datos.");
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $_SESSION['checkout_data'] = [
        'total' => $_POST['totalPedido'] ?? 0,
        'direccion' => $_POST['direccionEnvio'] ?? 'Dirección no proporcionada'
    ];

    $metodo_pago = $_POST['metodo_pago'] ?? 'tarjeta';

    if ($metodo_pago == 'bizum') {
        header("Location: ../controllers/pagoController.php?status=success");
        exit();
    }

    $line_items = [];
    $subtotal = 0;

    foreach ($_SESSION['carrito'] as $item) {

        $datosProd = $productoObj->obtenerProducto($item['idPrenda']);
        $nombreReal = $datosProd['nombre'];

        $rebaja = isset($datosProd['rebaja']) ? (int)$datosProd['rebaja'] : 0;
        $precioReal = $datosProd['precio'] - ($datosProd['precio'] * $rebaja / 100);

        $nombreStripe = $nombreReal . ' (Talla: ' . $item['talla'] . ')';
        if ($rebaja > 0) {
            $nombreStripe .= ' [REBAJA -' . $rebaja . '%]';
        }

        $subtotal += ($precioReal * $item['cantidad']);

        $line_items[] = [
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => $nombreReal . ' (Talla: ' . $item['talla'] . ')',
                ],
                'unit_amount' => round($precioReal * 100),
            ],
            'quantity' => $item['cantidad'],
        ];
    }

    if ($subtotal < 50) {
        $line_items[] = [
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => 'Gastos de envío',
                ],
                'unit_amount' => 499,
            ],
            'quantity' => 1,
        ];
    }

    $dominio = "http://" . $_SERVER['HTTP_HOST'];

    try {
        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $line_items,
            'mode' => 'payment',
            'success_url' => $dominio . '/controllers/pagoController.php?status=success',
            'cancel_url' => $dominio . '/checkout.php',
        ]);

        header("HTTP/1.1 303 See Other");
        header("Location: " . $checkout_session->url);
        exit();
    } catch (Exception $e) {
        die("Error al conectar con Stripe: " . $e->getMessage());
    }
}
