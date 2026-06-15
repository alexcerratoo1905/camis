<?php
require_once 'controllers/carritoController.php';
include './includes/header.php';
?>

<main class="container my-5 py-5 mt-5" style="min-height: 60vh;">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="display-6 fw-bold text-uppercase" style="letter-spacing: 2px;">Tu Cesta</h1>
            <p class="text-muted">
                <?php
                $numArticulos = 0;
                foreach ($carritoDetallado as $it) {
                    $numArticulos += $it['cantidad'];
                }
                echo $numArticulos;
                ?> artículo(s) seleccionados
            </p>
        </div>
    </div>

    <?php if (empty($carritoDetallado)): ?>
        <div class="row">
            <div class="col-12 text-center py-5 bg-light border">
                <i class="bi bi-bag-x display-1 text-muted mb-3 d-block"></i>
                <h3 class="fw-bold text-uppercase">Tu cesta está vacía</h3>
                <p class="text-muted mb-4">Parece que aún no has añadido nada.</p>
                <a href="catalogo.php" class="btn btn-dark rounded-0 px-5 py-3 text-uppercase fw-bold ls-1">Explorar Catálogo</a>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-5">
            <div class="col-lg-8">
                <?php foreach ($carritoDetallado as $item){ ?>
                    <div class="card border-0 border-bottom rounded-0 mb-3 pb-3">
                        <div class="row g-0">
                            
                            <div class="col-4 col-md-2">
                                <a href="fichaProducto.php?idPrenda=<?php echo $item['idPrenda']; ?>&color=<?php echo $item['color_id']; ?>">
                                    <img src="<?php echo $item['imagen']; ?>" class="img-fluid w-100 object-fit-cover" style="height: 140px;" alt="<?php echo $item['nombre']; ?>">
                                </a>
                            </div>
                            
                            <div class="col-8 col-md-10 px-3 d-flex flex-column justify-content-between">
                                
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="pe-2">
                                        <h5 class="fw-bold text-uppercase fs-6 mb-1">
                                            <a href="fichaProducto.php?idPrenda=<?php echo $item['idPrenda']; ?>&color=<?php echo $item['color_id']; ?>" class="text-decoration-none text-dark">
                                                <?php echo $item['nombre']; ?>
                                            </a>
                                        </h5>
                                        <p class="text-muted small mb-1">Color: <?php echo $item['color_nombre']; ?> | Talla: <?php echo $item['talla']; ?></p>
                                    </div>
                                    
                                    <div class="text-end d-none d-md-block">
                                        <?php if ($item['rebaja'] > 0){ ?>
                                            <span class="text-muted text-decoration-line-through small"><?php echo number_format($item['precio_original'] * $item['cantidad'], 2); ?> €</span><br>
                                            <span class="fw-bold fs-5 text-danger"><?php echo number_format($item['subtotal'], 2); ?> €</span>
                                            <div class="mt-1"><span class="badge bg-danger">-<?php echo $item['rebaja']; ?>%</span></div>
                                        <?php } else{ ?>
                                            <span class="fw-bold fs-5"><?php echo number_format($item['subtotal'], 2); ?> €</span>
                                        <?php } ?>
                                        <?php if ($item['cantidad'] > 1){ ?>
                                            <br><span class="text-muted small">(<?php echo $item['precio']; ?> €/u)</span>
                                        <?php } ?>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-end mt-2 mt-md-0">
                                    <div class="d-flex align-items-center gap-2 gap-md-3">
                                        <div class="d-flex align-items-center border border-dark rounded-0">
                                            <a href="controllers/carritoController.php?accion=restar&indice=<?php echo $item['indice']; ?>" class="btn btn-sm btn-light rounded-0 px-2 py-0 border-0" style="background: transparent;">-</a>
                                            <span class="px-3 fw-bold border-start border-end border-dark" style="font-size: 0.9rem;"><?php echo $item['cantidad']; ?></span>
                                            <a href="controllers/carritoController.php?accion=sumar&indice=<?php echo $item['indice']; ?>" class="btn btn-sm btn-light rounded-0 px-2 py-0 border-0" style="background: transparent;">+</a>
                                        </div>
                                        
                                        <a href="controllers/carritoController.php?accion=eliminar&indice=<?php echo $item['indice']; ?>" class="text-danger small text-decoration-none">
                                            <i class="bi bi-trash fs-5 d-md-none"></i>
                                            <span class="d-none d-md-inline text-decoration-underline">Eliminar</span>
                                        </a>
                                    </div>
                                    
                                    <div class="text-end d-block d-md-none">
                                        <?php if ($item['rebaja'] > 0){ ?>
                                            <span class="text-muted text-decoration-line-through small d-block" style="font-size: 0.75rem;"><?php echo number_format($item['precio_original'] * $item['cantidad'], 2); ?> €</span>
                                            <span class="fw-bold fs-6 text-danger"><?php echo number_format($item['subtotal'], 2); ?> €</span>
                                        <?php } else{ ?>
                                            <span class="fw-bold fs-6"><?php echo number_format($item['subtotal'], 2); ?> €</span>
                                        <?php } ?>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-0 p-4 bg-light">
                    <h4 class="fw-bold text-uppercase mb-4">Resumen</h4>

                    <div class="d-flex justify-content-between mb-3 text-muted">
                        <span>Subtotal</span>
                        <span><?php echo number_format($totalCarrito, 2); ?> €</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 text-muted border-bottom pb-3">
                        <span>Gastos de envío</span>
                        <span>4.99</span>
                    </div>



                    <div class="mb-4 p-3 bg-light border border-dark">
                        <label class="form-label fw-bold text-uppercase small" style="letter-spacing: 1px;">¿Tienes un código de descuento?</label>

                        <?php if (isset($_GET['error'])){ ?>
                            <div class="alert alert-danger py-2 rounded-0 small fw-bold mb-3 border-2 border-danger">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <?php
                                if ($_GET['error'] == 'no_sesion') {
                                    echo 'Debes iniciar sesión para usar un código.';
                                } elseif ($_GET['error'] == 'codigo_invalido') {
                                    echo 'El código no existe, está caducado o no está vinculado a tu correo.';
                                }
                                ?>
                            </div>
                        <?php }; ?>

                        <?php if (isset($_SESSION['descuento'])){ ?>
                            <div class="alert alert-success m-0 py-2 d-flex justify-content-between align-items-center rounded-0 border-success border-2 fw-bold">
                                <span><i class="bi bi-tag-fill me-2"></i> Código <strong><?= $_SESSION['descuento']['codigo'] ?></strong> (-<?= $_SESSION['descuento']['porcentaje'] ?>%)</span>
                                <a href="controllers/carritoController.php?accion=quitar_descuento" class="text-danger text-decoration-none" title="Quitar descuento"><i class="bi bi-x-lg"></i></a>
                            </div>
                        <?php }else { ?>
                            <form action="controllers/carritoController.php" method="POST" class="m-0">
                                <input type="hidden" name="accion" value="aplicar_descuento">
                                <div class="input-group">
                                    <input type="text" name="codigo_descuento" class="form-control rounded-0 text-uppercase" placeholder="Tu código" required>
                                    <button type="submit" class="btn btn-dark rounded-0 text-uppercase fw-bold">Aplicar</button>
                                </div>
                            </form>
                        <?php }; ?>
                    </div>


                    <?php
                    $descuentoCantidad = 0;
                    if (isset($_SESSION['descuento'])) {
                        $porcentaje = $_SESSION['descuento']['porcentaje'];
                        $descuentoCantidad = $totalCarrito * ($porcentaje / 100);
                    }

                    $envio = ($totalCarrito > 50) ? 0 : 4.99;

                    $totalFinal = ($totalCarrito - $descuentoCantidad) + $envio;
                    ?>

                    <div class="d-flex justify-content-between mb-4 mt-3">
                        <span class="fw-bold text-uppercase">Total</span>
                        <span class="fw-bold fs-4"><?php echo number_format($totalFinal, 2); ?> €</span>
                    </div>
                    <?php if ($descuentoCantidad > 0) { ?>
                        <div class="d-flex justify-content-between mb-2 text-danger fw-bold">
                            <span>Descuento (<?= $_SESSION['descuento']['porcentaje'] ?>%)</span>
                            <span>-<?= number_format($descuentoCantidad, 2) ?> €</span>
                        </div>
                    <?php }; ?>

                    <?php

                    $urlCorrecta = (isset($_SESSION["usuario_id"])) ? "checkout.php" : "index.php?mensaje=login_requerido";

                    ?>

                    <a href="<?php echo $urlCorrecta ?>" class="btn btn-dark rounded-0 py-3 text-uppercase fw-bold w-100 ls-1">Tramitar Pedido</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php include './includes/footer.php'; ?>