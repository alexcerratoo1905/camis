<?php
require_once 'includes/auth.php';
require_once 'config/db.php';
require_once 'models/pedido.php';
require_once 'models/producto.php';

$db = new Database();
$conexion = $db->conectar();
$pedidoModel = new Pedido($conexion);
$productoModel = new Producto($conexion);

if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
    
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

    // ---> NUEVOS TRAMOS DE DESCUENTO POR VOLUMEN <---
    $porcentajeAuto = 0;
    if ($numArticulos >= 10) {
        $porcentajeAuto = 20;
    } elseif ($numArticulos >= 5) {
        $porcentajeAuto = 15;
    } elseif ($numArticulos >= 3) {
        $porcentajeAuto = 10;
    }

    $porcentajeManual = isset($_SESSION['descuento']) ? (int)$_SESSION['descuento']['porcentaje'] : 0;
    $porcentajeFinal = max($porcentajeAuto, $porcentajeManual);
    $factorMultiplicador = 1 - ($porcentajeFinal / 100);
    $descuentoCantidad = $subtotalCheckout * ($porcentajeFinal / 100);

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

    $totalFinalFactura = ($subtotalCheckout - $descuentoCantidad) + $envio;
    $direccionPedido = $_SESSION['direccion_pedido_temporal'] ?? 'Dirección no especificada';

    $idNuevoPedido = $pedidoModel->crearPedido($_SESSION['usuario_id'], $totalFinalFactura, $direccionPedido);

    foreach ($_SESSION['carrito'] as $item) {
        $producto = $productoModel->obtenerProducto($item['idPrenda']);
        $rebaja = isset($producto['rebaja']) ? (int)$producto['rebaja'] : 0;
        $precioBase = $producto['precio'] - ($producto['precio'] * $rebaja / 100);
        
        $extraPrecio = 0;
        $textoExtrasArray = [];
        
        $versionElegida = isset($item['version_genero']) ? ucfirst($item['version_genero']) : 'Hombre';
        $textoExtrasArray[] = "Versión: " . $versionElegida;
        
        if (!empty($item['extra_player'])) { $extraPrecio += 3; $textoExtrasArray[] = "Player"; }
        if (!empty($item['extra_pantalon'])) { $extraPrecio += 10; $textoExtrasArray[] = "+Pantalón"; }
        if (!empty($item['tiene_parche'])) { $extraPrecio += 1; $textoExtrasArray[] = "Parche: " . $item['texto_parche']; }
        if (!empty($item['tiene_personalizacion'])) { $extraPrecio += 2; $textoExtrasArray[] = "Dorsal: " . $item['texto_nombre'] . " " . $item['texto_numero']; }
        if (in_array($item['talla'], ['2XL', '3XL', '4XL'])) $extraPrecio += 1;
        
        $precioUnitarioFinal = $precioBase + $extraPrecio;
        $precioUnitarioDescontado = $precioUnitarioFinal * $factorMultiplicador;
        $stringExtras = empty($textoExtrasArray) ? null : implode(" | ", $textoExtrasArray);

        $pedidoModel->crearDetallesPedidos(
            $idNuevoPedido, 
            $item['idPrenda'], 
            $item['color_id'], 
            $item['talla'], 
            $item['cantidad'], 
            $precioUnitarioDescontado, 
            $stringExtras
        );
    }

    unset($_SESSION['carrito']);
    unset($_SESSION['descuento']);
    unset($_SESSION['direccion_pedido_temporal']);
}

include './includes/header.php';
?>
<main id="graciasCompra" class="container my-5 py-5 mt-5 d-flex justify-content-center align-items-center" style="min-height: 60vh;">
    <div class="card border-dark border-3 rounded-0 shadow-lg text-center p-5" style="max-width: 600px;">
        <div class="card-body">
            
            <i class="bi bi-check-circle-fill text-success animate__animated animate__bounceIn" style="font-size: 5rem;"></i>
            
            <h1 class="display-5 fw-bold text-uppercase mt-4 mb-3">¡Pedido Confirmado!</h1>
            <p class="fs-5 text-muted mb-5">Hemos recibido tu pago correctamente y el pedido ha quedado registrado. Estamos preparándolo con todo el cuidado que se merece.</p>
            
            <div class="d-flex justify-content-center align-items-center mb-3">
                <div class="spinner-border text-dark" role="status" style="width: 3rem; height: 3rem; border-width: 0.25em;">
                    <span class="visually-hidden">Procesando...</span>
                </div>
            </div>
            
            <p class="small text-uppercase fw-bold text-muted mt-3">Redirigiendo a tu perfil para ver los detalles...</p>
            
        </div>
    </div>
</main>

<script>
    setTimeout(function() {
        window.location.href = 'perfil.php?seccion=pedidos';
    }, 3000);
</script>

<?php include './includes/footer.php'; ?>