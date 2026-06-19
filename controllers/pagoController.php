<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/producto.php';

// =========================================================================
// ¡PON AQUÍ TU CLAVE SECRETA DE STRIPE (sk_test_...)
// =========================================================================
\Stripe\Stripe::setApiKey('sk_test_51TRSRfHJPlhS3OiOmWvQ9M4K1TuNPsHDsBNsV9l99ziXgumDDGjjQtGNQNprptcmSqS0QYrdrGx4AMaOr2HAcy5o006E97tSH6');

$db = new Database();
$conexion = $db->conectar();
$productoModel = new Producto($conexion);

$metodoPago = $_POST['metodo_pago'] ?? 'tarjeta';
if ($metodoPago === 'bizum') {
    header('Location: ../gracias.php?metodo=bizum');
    exit;
}

if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    header('Location: ../carrito.php');
    exit;
}

$subtotalCheckout = 0;
$numArticulos = 0;

foreach ($_SESSION['carrito'] as $item) {
    $producto = $productoModel->obtenerProducto($item['idPrenda']);
    $rebaja = isset($producto['rebaja']) ? (int)$producto['rebaja'] : 0;
    $precioBase = $producto['precio'] - ($producto['precio'] * $rebaja / 100);
    
    $extraPrecio = 0;
    if (!empty($item['extra_player'])) $extraPrecio += 3;
    if (!empty($item['extra_pantalon'])) $extraPrecio += 10;
    if (!empty($item['tiene_parche'])) $extraPrecio += 1;
    if (!empty($item['tiene_personalizacion'])) $extraPrecio += 2;
    if (in_array($item['talla'], ['2XL', '3XL', '4XL'])) $extraPrecio += 1;
    
    $precioUnitarioFinal = $precioBase + $extraPrecio;
    $subtotalCheckout += ($precioUnitarioFinal * $item['cantidad']);
    $numArticulos += $item['cantidad'];
}

$porcentajeAuto = 0;
if ($numArticulos >= 5 || $subtotalCheckout > 120) {
    $porcentajeAuto = 15;
} elseif ($numArticulos > 3 || $subtotalCheckout > 75) {
    $porcentajeAuto = 10;
}
$porcentajeManual = isset($_SESSION['descuento']) ? (int)$_SESSION['descuento']['porcentaje'] : 0;
$porcentajeFinal = max($porcentajeAuto, $porcentajeManual);
$factorMultiplicador = 1 - ($porcentajeFinal / 100);

$envio = 0;
if ($numArticulos == 1) {
    $envio = 4.99;
} elseif ($numArticulos == 2 || $numArticulos == 3) {
    $envio = 2.99;
} elseif ($numArticulos == 4) {
    $envio = 1.99;
} else {
    $envio = 0.00;
}

$lineItems = [];

foreach ($_SESSION['carrito'] as $item) {
    $producto = $productoModel->obtenerProducto($item['idPrenda']);
    $rebaja = isset($producto['rebaja']) ? (int)$producto['rebaja'] : 0;
    $precioBase = $producto['precio'] - ($producto['precio'] * $rebaja / 100);
    
    $extraPrecio = 0;
    
    // AÑADIMOS LA VERSIÓN AL TICKET DE STRIPE
    $versionElegida = isset($item['version_genero']) ? ucfirst($item['version_genero']) : 'Hombre';
    $detalles = "Versión: " . $versionElegida . " | Talla: " . $item['talla'];
    
    if (!empty($item['extra_player'])) { $extraPrecio += 3; $detalles .= " | Player"; }
    if (!empty($item['extra_pantalon'])) { $extraPrecio += 10; $detalles .= " | +Pantalón"; }
    if (!empty($item['tiene_parche'])) { $extraPrecio += 1; $detalles .= " | Parches"; }
    if (!empty($item['tiene_personalizacion'])) { $extraPrecio += 2; $detalles .= " | Nombre"; }
    if (in_array($item['talla'], ['2XL', '3XL', '4XL'])) $extraPrecio += 1;
    
    $precioUnitarioFinal = $precioBase + $extraPrecio;
    $precioConDescuento = round(($precioUnitarioFinal * $factorMultiplicador) * 100); 
    
    if ($porcentajeFinal > 0) {
        $detalles .= " (-$porcentajeFinal% Dcto)";
    }

    $lineItems[] = [
        'price_data' => [
            'currency' => 'eur',
            'product_data' => [
                'name' => $producto['nombre'],
                'description' => $detalles,
            ],
            'unit_amount' => $precioConDescuento,
        ],
        'quantity' => $item['cantidad'],
    ];
}

if ($envio > 0) {
    $lineItems[] = [
        'price_data' => [
            'currency' => 'eur',
            'product_data' => [
                'name' => 'Gastos de envío',
                'description' => 'Envío de ' . $numArticulos . ' prenda(s)',
            ],
            'unit_amount' => round($envio * 100),
        ],
        'quantity' => 1,
    ];
}

if (isset($_POST['direccionEnvio'])) {
    $_SESSION['direccion_pedido_temporal'] = $_POST['direccionEnvio'];
}

try {
    $protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $dominio = $protocolo . "://" . $_SERVER['HTTP_HOST'];
    $rutaBase = str_replace('/controllers/pagoController.php', '', $_SERVER['REQUEST_URI']);
    $urlBase = $dominio . $rutaBase;

    $checkout_session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => $lineItems,
        'mode' => 'payment',
        'success_url' => $urlBase . '/gracias.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => $urlBase . '/checkout.php?error=pago_cancelado',
    ]);

    header("HTTP/1.1 303 See Other");
    header("Location: " . $checkout_session->url);
    exit;
} catch (Exception $e) {
    die("Error de conexión con Stripe: " . $e->getMessage());
}
?>