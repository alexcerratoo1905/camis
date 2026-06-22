<?php
require_once 'controllers/checkoutController.php';
include './includes/header.php';
?>
<main class="container my-5 py-5 mt-5" style="min-height: 60vh;">
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-6 fw-bold text-uppercase" style="letter-spacing: 2px;">Confirmar Pedido</h1>
            <p class="text-muted">Revisa tus datos de envío y finaliza la compra</p>
        </div>
    </div>
    
    <form id="formPago" action="controllers/pagoController.php" method="POST">
        <div class="row g-5">
            
            <div class="col-lg-7">
                <h4 class="fw-bold text-uppercase mb-4 border-bottom pb-2">1. Datos de Envío</h4>
                <div class="row g-3 mb-5">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase">Nombre</label>
                        <input type="text" class="form-control rounded-0 border-dark" id="envio_nombre" value="<?php echo htmlspecialchars($datosComprador['nombre'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase">Apellidos</label>
                        <input type="text" class="form-control rounded-0 border-dark" id="envio_apellidos" value="<?php echo htmlspecialchars($datosComprador['apellidos'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label small fw-bold text-muted text-uppercase">Teléfono de Contacto</label>
                        <input type="text" class="form-control rounded-0 border-dark" id="envio_telefono" value="<?php echo htmlspecialchars($datosComprador['telefono'] ?? ''); ?>" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold text-muted text-uppercase">Dirección (Calle, número, piso)</label>
                        <input type="text" class="form-control rounded-0 border-dark" id="envio_direccion" value="<?php echo htmlspecialchars($datosComprador['direccion'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted text-uppercase">C.P.</label>
                        <input type="text" class="form-control rounded-0 border-dark" id="envio_cp" value="<?php echo htmlspecialchars($datosComprador['codigo_postal'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label small fw-bold text-muted text-uppercase">Ciudad</label>
                        <input type="text" class="form-control rounded-0 border-dark" id="envio_ciudad" value="<?php echo htmlspecialchars($datosComprador['ciudad'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted text-uppercase">Provincia</label>
                        <input type="text" class="form-control rounded-0 border-dark" id="envio_provincia" value="<?php echo htmlspecialchars($datosComprador['provincia'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted text-uppercase">Comunidad Autónoma</label>
                        <input type="text" class="form-control rounded-0 border-dark" id="envio_ca" value="<?php echo htmlspecialchars($datosComprador['comunidad_autonoma'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted text-uppercase">País</label>
                        <input type="text" class="form-control rounded-0 border-dark" id="envio_pais" value="<?php echo htmlspecialchars($datosComprador['pais'] ?? 'España'); ?>" required>
                    </div>
                </div>

                <h4 class="fw-bold text-uppercase mb-4 border-bottom pb-2">2. Método de Pago</h4>
                <div class="card border border-dark border-2 rounded-0 p-3 bg-white mb-3" id="caja_tarjeta">
                    <div class="form-check d-flex align-items-center m-0">
                        <input class="form-check-input me-3" type="radio" name="metodo_pago" id="pago_tarjeta" value="tarjeta" checked style="transform: scale(1.2);" onchange="cambiarMetodoPago()">
                        <label class="form-check-label fw-bold w-100 d-flex justify-content-between align-items-center" for="pago_tarjeta" style="cursor: pointer;">
                            <span>Tarjeta de Crédito / Débito</span>
                            <i class="bi bi-credit-card-2-back fs-4"></i>
                        </label>
                    </div>
                    <div id="form_tarjeta" class="mt-3 pt-3 border-top">
                        <p class="text-muted small mb-0"><i class="bi bi-shield-check text-success me-1"></i> <strong>Pago 100% Seguro.</strong> Al hacer clic en "Confirmar y Pagar", serás redirigido a la pasarela segura de pago de Stripe.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-0 p-4 bg-light sticky-top" style="top: 100px;">
                    <h4 class="fw-bold text-uppercase mb-4">Resumen del Pedido</h4>
                    
                    <div class="border-bottom pb-3 mb-3" style="max-height: 250px; overflow-y: auto;">
                        <?php
                        $numArticulos = 0;
                        $subtotalCheckout = 0;
                        if (isset($_SESSION['carrito'])):
                            foreach ($_SESSION['carrito'] as $item):
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
                                $subtotalItem = $precioUnitarioFinal * $item['cantidad'];
                                
                                $numArticulos += $item['cantidad'];
                                $subtotalCheckout += $subtotalItem;
                        ?>
                                <div class="d-flex justify-content-between mb-3 small align-items-center border-bottom pb-2">
                                    <div class="text-truncate pe-2" style="flex: 1; min-width: 0;">
                                        <span class="fw-bold"><?php echo $item['cantidad']; ?>x</span>
                                        <?php echo $producto['nombre']; ?>
                                        <span class="text-muted">(<?php echo $item['talla']; ?> | Versión: <?php echo ucfirst($item['version_genero'] ?? 'Hombre'); ?>)</span>
                                    </div>
                                    <div class="text-nowrap text-end flex-shrink-0 ps-2">
                                        <span class="fw-bold d-block"><?php echo number_format($subtotalItem, 2); ?> €</span>
                                    </div>
                                </div>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </div>

                    <?php
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
                    $descuentoCantidad = $subtotalCheckout * ($porcentajeFinal / 100);
                    $totalFinalCheckout = ($subtotalCheckout - $descuentoCantidad) + $envio;
                    ?>

                    <div class="d-flex justify-content-between mb-2 text-muted">
                        <span>Subtotal</span>
                        <span><?php echo number_format($subtotalCheckout, 2); ?> €</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 text-muted border-bottom pb-3">
                        <span>Gastos de envío</span>
                        <?php if ($envio == 0): ?>
                            <span class="text-success fw-bold">GRATIS</span>
                        <?php else: ?>
                            <span class="fw-bold"><?php echo number_format($envio, 2); ?> €</span>
                        <?php endif; ?>
                    </div>
                    <?php if ($porcentajeFinal > 0): ?>
                        <div class="d-flex justify-content-between mb-2 text-danger fw-bold bg-danger bg-opacity-10 p-2">
                            <?php if ($porcentajeFinal == $porcentajeAuto): ?>
                                <span>Dcto. Volumen (-<?= $porcentajeFinal ?>%)</span>
                            <?php else: ?>
                                <span>Cupón (-<?= $porcentajeFinal ?>%)</span>
                            <?php endif; ?>
                            <span>-<?= number_format($descuentoCantidad, 2) ?> €</span>
                        </div>
                    <?php endif; ?>
                    <div class="d-flex justify-content-between mb-4 mt-3 border-top border-dark pt-3">
                        <span class="fw-bold text-uppercase fs-5">Total a Pagar</span>
                        <span class="fw-bold fs-3"><?php echo number_format($totalFinalCheckout, 2); ?> €</span>
                    </div>

                    <input type="hidden" name="totalPedido" value="<?php echo $totalFinalCheckout; ?>">
                    <input type="hidden" name="direccionEnvio" id="direccionEnvioFinal" value="">
                    
                    <button type="submit" class="btn btn-dark rounded-0 py-3 text-uppercase fw-bold w-100 ls-1">
                        Confirmar y Pagar
                    </button>
                </div>
            </div>
        </div>
    </form>
</main>

<script>
    document.getElementById('formPago').addEventListener('submit', function(e) {
        let nom = document.getElementById('envio_nombre').value + ' ' + document.getElementById('envio_apellidos').value;
        let dir = document.getElementById('envio_direccion').value;
        let ciu = document.getElementById('envio_ciudad').value;
        let cp = document.getElementById('envio_cp').value;
        let prov = document.getElementById('envio_provincia').value;
        let ca = document.getElementById('envio_ca').value;
        let pais = document.getElementById('envio_pais').value;
        let tlf = document.getElementById('envio_telefono').value;
        
        let stringEnvio = nom + " | " + dir + ", " + cp + " " + ciu + ", " + prov + " (" + ca + "), " + pais + " | Tlf: " + tlf;
        
        document.getElementById('direccionEnvioFinal').value = stringEnvio;
    });
</script>

<script src="public/js/checkout.js"></script>
<?php include './includes/footer.php'; ?>