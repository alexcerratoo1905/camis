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
                <h4 class="fw-bold text-uppercase mb-4 border-bottom pb-2 mt-5">2. Método de Pago</h4>

                <div class="card border border-dark border-2 rounded-0 p-3 bg-white mb-3" id="caja_tarjeta">
                    <div class="form-check d-flex align-items-center m-0">
                        <input class="form-check-input me-3" type="radio" name="metodo_pago" id="pago_tarjeta" value="tarjeta" checked style="transform: scale(1.2);" onchange="cambiarMetodoPago()">
                        <label class="form-check-label fw-bold w-100 d-flex justify-content-between align-items-center" for="pago_tarjeta" style="cursor: pointer;">
                            <span>Tarjeta de Crédito / Débito</span>
                            <i class="bi bi-credit-card-2-back fs-4"></i>
                        </label>
                    </div>
                    <div id="form_tarjeta" class="mt-3 pt-3 border-top">
                        <p class="text-muted small mb-0"><i class="bi bi-shield-check text-success me-1"></i> <strong>Pago 100% Seguro.</strong> Al hacer clic en "Confirmar y Pagar", serás redirigido a la pasarela segura de pago.</p>
                    </div>
                </div>

                <div class="card border border-secondary border-1 rounded-0 p-3 bg-light mb-3" id="caja_bizum" style="opacity: 0.7;">
                    <div class="form-check d-flex align-items-center m-0">
                        <input class="form-check-input me-3" type="radio" name="metodo_pago" id="pago_bizum" value="bizum" style="transform: scale(1.2);" onchange="cambiarMetodoPago()">
                        <label class="form-check-label fw-bold w-100 d-flex justify-content-between align-items-center" for="pago_bizum" style="cursor: pointer;">
                            <span>Pago con Bizum</span>
                            <i class="bi bi-phone fs-4"></i>
                        </label>
                    </div>
                </div>

                <p class="text-muted small mt-3"><i class="bi bi-shield-lock me-1"></i> Tus datos están protegidos por normativa europea PSD2.</p>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-0 p-4 bg-light sticky-top" style="top: 100px;">
                    <h4 class="fw-bold text-uppercase mb-4">Resumen del Pedido</h4>

                    <div class="border-bottom pb-3 mb-3" style="max-height: 250px; overflow-y: auto;">
                        <?php
                        $subtotalCheckout = 0;
                        if (isset($_SESSION['carrito'])):
                            foreach ($_SESSION['carrito'] as $item):
                                $producto = $productoModel->obtenerProducto($item['idPrenda']);

                                $rebaja = isset($producto['rebaja']) ? (int)$producto['rebaja'] : 0;
                                $precioUnitario = $producto['precio'] - ($producto['precio'] * $rebaja / 100);

                                $subtotalItem = $precioUnitario * $item['cantidad'];
                                $subtotalOriginal = $producto['precio'] * $item['cantidad'];
                                $subtotalCheckout += $subtotalItem;
                        ?>
                                <div class="d-flex justify-content-between mb-3 small align-items-center border-bottom pb-2">
                                    <div class="text-truncate pe-2" style="flex: 1; min-width: 0;">
                                        <span class="fw-bold"><?php echo $item['cantidad']; ?>x</span>
                                        <?php echo $producto['nombre']; ?>
                                        <span class="text-muted">(<?php echo $item['talla']; ?>)</span>
                                        <?php if ($rebaja > 0): ?>
                                            <span class="badge bg-danger ms-1">-<?php echo $rebaja; ?>%</span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- flex-shrink-0 asegura que el precio NUNCA se aplaste o cambie de línea -->
                                    <div class="text-nowrap text-end flex-shrink-0 ps-2">
                                        <?php if ($rebaja > 0): ?>
                                            <span class="text-muted text-decoration-line-through d-block" style="font-size: 0.75rem;"><?php echo number_format($subtotalOriginal, 2); ?> €</span>
                                            <span class="fw-bold text-danger"><?php echo number_format($subtotalItem, 2); ?> €</span>
                                        <?php else: ?>
                                            <span class="fw-bold d-block mt-3"><?php echo number_format($subtotalItem, 2); ?> €</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </div>

                    <div class="d-flex justify-content-between mb-2 text-muted">
                        <span>Subtotal</span>
                        <span><?php echo number_format($subtotalCheckout, 2); ?> €</span>
                    </div>

                    <?php
                    $descuentoCheckout = 0;
                    if (isset($_SESSION['descuento'])) {
                        $porcentaje = $_SESSION['descuento']['porcentaje'];
                        $descuentoCheckout = $subtotalCheckout * ($porcentaje / 100);
                    }

                    if ($descuentoCheckout > 0): ?>
                        <div class="d-flex justify-content-between mb-2 text-danger fw-bold">
                            <span>Descuento (<?= $_SESSION['descuento']['porcentaje'] ?>%)</span>
                            <span>-<?= number_format($descuentoCheckout, 2) ?> €</span>
                        </div>
                    <?php endif; ?>

                    <?php
                    $envio = (($subtotalCheckout - $descuentoCheckout) > 50) ? 0 : 4.99;
                    $totalFinalCheckout = ($subtotalCheckout - $descuentoCheckout) + $envio;
                    ?>

                    <div class="d-flex justify-content-between mb-3 text-muted border-bottom pb-3">
                        <span>Gastos de envío</span>
                        <?php if ($envio == 0): ?>
                            <span class="text-success fw-bold">GRATIS</span>
                        <?php else: ?>
                            <span class="fw-bold"><?php echo number_format($envio, 2); ?> €</span>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex justify-content-between mb-4 mt-3">
                        <span class="fw-bold text-uppercase fs-5">Total a Pagar</span>
                        <span class="fw-bold fs-3"><?php echo number_format($totalFinalCheckout, 2); ?> €</span>
                    </div>

                    <input type="hidden" name="totalPedido" value="<?php echo $totalFinalCheckout; ?>">
                    <input type="hidden" name="direccionEnvio" value="<?php echo $datosComprador['direccion'] . ' - ' . $datosComprador['ciudad'] . ' (' . $datosComprador['codigo_postal'] . ')'; ?>">

                    <?php
                    $btnDisabled = (empty($datosComprador['direccion']) || empty($datosComprador['ciudad'])) ? 'disabled' : '';
                    ?>

                    <button type="submit" class="btn btn-dark rounded-0 py-3 text-uppercase fw-bold w-100 ls-1" <?php echo $btnDisabled; ?>>
                        Confirmar y Pagar
                    </button>

                    <?php if ($btnDisabled): ?>
                        <div class="text-danger small mt-2 text-center fw-bold">
                            Debes completar tu dirección de envío en el perfil para poder pagar.
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </form>
</main>

<script src="public/js/checkout.js"></script>

<?php include './includes/footer.php'; ?>