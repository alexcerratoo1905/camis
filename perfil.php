<?php
require_once 'includes/auth.php';
require_once 'controllers/perfilController.php';

$seccion = isset($_GET['seccion']) ? $_GET['seccion'] : 'datos';
include './includes/header.php';
?>

<main class="container my-5 py-5 mt-5">
    <div class="row">
        <aside class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm rounded-0 position-sticky" style="top: 70px; z-index: 10;">
                <div class="card-body p-0">
                    <div class="p-4 border-bottom bg-light d-none d-lg-block">
                        <h5 class="fw-bold mb-1 text-uppercase">Mi Cuenta</h5>
                        <p class="text-muted small mb-0 text-truncate" title="<?php echo isset($datosUsu['email']) ? $datosUsu['email'] : ''; ?>"><?php echo isset($datosUsu['email']) ? $datosUsu['email'] : ''; ?></p>
                    </div>
                    <style>
                        .menu-perfil-scroll::-webkit-scrollbar {
                            display: none;
                        }
                        .menu-perfil-scroll {
                            scrollbar-width: none;
                        }
                    </style>
                    <div class="list-group list-group-flush rounded-0 flex-row flex-lg-column overflow-auto menu-perfil-scroll" style="white-space: nowrap; -webkit-overflow-scrolling: touch;">
                        <a href="perfil.php?seccion=datos" class="list-group-item list-group-item-action p-3 fw-bold border-0 border-bottom-lg border-end border-lg-0 <?php echo $seccion == 'datos' ? 'bg-dark text-white' : 'text-muted'; ?>">Mis Datos</a>
                        <a href="perfil.php?seccion=pedidos" class="list-group-item list-group-item-action p-3 fw-bold border-0 border-bottom-lg border-end border-lg-0 <?php echo $seccion == 'pedidos' ? 'bg-dark text-white' : 'text-muted'; ?>">Mis Pedidos</a>
                        <a href="perfil.php?seccion=favoritos" class="list-group-item list-group-item-action p-3 fw-bold border-0 border-bottom-lg border-end border-lg-0 <?php echo $seccion == 'favoritos' ? 'bg-dark text-white' : 'text-muted'; ?>">Mis Favoritos</a>
                        <a href="perfil.php?seccion=citas" class="list-group-item list-group-item-action p-3 fw-bold border-0 border-bottom-lg border-end border-lg-0 <?php echo $seccion == 'citas' ? 'bg-dark text-white' : 'text-muted'; ?>">Mis Citas</a>
                        <a href="perfil.php?seccion=prendas" class="list-group-item list-group-item-action p-3 fw-bold border-0 border-bottom-lg border-end border-lg-0 <?php echo $seccion == 'prendas' ? 'bg-dark text-white' : 'text-muted'; ?>">Mi Armario</a>
                        <a href="controllers/usuarioController.php?accion=logout" class="list-group-item list-group-item-action p-3 text-danger fw-bold border-0 mt-lg-2 border-top-lg">
                            <i class="bi bi-box-arrow-right d-lg-none fs-5"></i>
                            <span class="d-none d-lg-inline">Cerrar Sesión</span>
                        </a>
                    </div>
                </div>
            </div>
        </aside>

        <section class="col-lg-9">
            <?php if ($seccion == 'datos') { ?>
                <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] == 'perfil_actualizado'): ?>
                    <div class="alert alert-success rounded-0 text-uppercase fw-bold text-center mb-4"> Datos actualizados correctamente
                    </div>
                <?php endif; ?>
                <div class="card border-0 shadow-sm rounded-0 p-4">
                    <h3 class="fw-bold text-uppercase mb-4">Datos Personales</h3>
                    <form action="controllers/perfilController.php" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small fw-bold text-uppercase">Nombre</label>
                                <input type="text" class="form-control rounded-0 border-dark" name="nombre" value="<?php echo htmlspecialchars($datosUsu['nombre'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small fw-bold text-uppercase">Apellidos</label>
                                <input type="text" class="form-control rounded-0 border-dark" name="apellidos" value="<?php echo htmlspecialchars($datosUsu['apellidos'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small fw-bold text-uppercase">Email (Fijo)</label>
                                <input type="email" class="form-control rounded-0 text-muted border-dark" value="<?php echo htmlspecialchars($datosUsu['email'] ?? ''); ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label text-muted small fw-bold text-uppercase">Teléfono</label>
                                <input type="text" class="form-control rounded-0 border-dark" name="telefono" value="<?php echo htmlspecialchars($datosUsu['telefono'] ?? ''); ?>">
                            </div>
                        </div>

                        <h3 class="fw-bold text-uppercase mb-4 mt-3 border-top pt-4">Dirección de Envío</h3>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label text-muted small fw-bold text-uppercase">Dirección Completa (Calle, número, piso)</label>
                                <input type="text" class="form-control rounded-0 border-dark" name="direccion" value="<?php echo htmlspecialchars($datosUsu['direccion'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-bold text-uppercase">Código Postal</label>
                                <input type="text" class="form-control rounded-0 border-dark" name="codigoPostal" value="<?php echo htmlspecialchars($datosUsu['codigo_postal'] ?? ''); ?>">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label text-muted small fw-bold text-uppercase">Ciudad</label>
                                <input type="text" class="form-control rounded-0 border-dark" name="ciudad" value="<?php echo htmlspecialchars($datosUsu['ciudad'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-bold text-uppercase">Provincia</label>
                                <input type="text" class="form-control rounded-0 border-dark" name="provincia" value="<?php echo htmlspecialchars($datosUsu['provincia'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-bold text-uppercase">Comunidad Autónoma</label>
                                <input type="text" class="form-control rounded-0 border-dark" name="comunidad_autonoma" value="<?php echo htmlspecialchars($datosUsu['comunidad_autonoma'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-bold text-uppercase">País</label>
                                <input type="text" class="form-control rounded-0 border-dark" name="pais" value="<?php echo htmlspecialchars($datosUsu['pais'] ?? 'España'); ?>">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-dark rounded-0 px-5 py-3 text-uppercase fw-bold ls-1 w-100">Guardar Cambios</button>
                    </form>
                </div>

                <div class="card border-0 shadow-sm rounded-0 p-4 bg-white mt-4">
                    <h4 class="fw-bold text-uppercase mb-4 border-bottom pb-2">
                        <i class="bi bi-shield-lock me-2"></i>Cambiar Contraseña
                    </h4>
                    <?php if (isset($_GET['error']) && $_GET['error'] == 'passNoCoinciden'): ?>
                        <div class="alert alert-danger rounded-0 small fw-bold text-uppercase text-center">
                            <i class="bi bi-exclamation-triangle me-1"></i> Las nuevas contraseñas no coinciden.
                        </div>
                    <?php endif; ?>
                    <?php if (isset($_GET['error']) && $_GET['error'] == 'passActualFalsa'): ?>
                        <div class="alert alert-danger rounded-0 small fw-bold text-uppercase text-center">
                            <i class="bi bi-exclamation-triangle me-1"></i> La contraseña actual es incorrecta.
                        </div>
                    <?php endif; ?>
                    <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] == 'passActualizada'): ?>
                        <div class="alert alert-success rounded-0 small fw-bold text-uppercase text-center">
                            <i class="bi bi-check-circle me-1"></i> ¡Contraseña actualizada con éxito!
                        </div>
                    <?php endif; ?>
                    <form action="controllers/perfilController.php" method="POST">
                        <input type="hidden" name="accion" value="cambiarPass">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-bold text-uppercase">Contraseña Actual</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 rounded-0"><i class="bi bi-key text-muted"></i></span>
                                    <input type="password" name="passActual" class="form-control rounded-0 border-start-0" placeholder="Introduce tu clave actual" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-uppercase">Nueva Contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 rounded-0"><i class="bi bi-lock text-muted"></i></span>
                                    <input type="password" name="nuevaPass" class="form-control rounded-0 border-start-0" placeholder="Mín. 6 caracteres" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-uppercase">Confirmar Nueva Contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 rounded-0"><i class="bi bi-check-all text-muted"></i></span>
                                    <input type="password" name="confirmarCambioPass" class="form-control rounded-0 border-start-0" placeholder="Repite la clave" required>
                                </div>
                            </div>
                            <div class="col-12 mt-4 text-end">
                                <button type="submit" class="btn btn-dark rounded-0 px-4 py-2 text-uppercase fw-bold ls-1">
                                    Actualizar Contraseña
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            <?php } elseif ($seccion == 'pedidos') { ?>
                <!-- TODO LO DEMÁS DEL PERFIL SE MANTIENE EXACTAMENTE IGUAL -->
                <?php if (empty($listaPedidos)) { ?>
                    <div class="card border-0 shadow-sm rounded-0 p-5 text-center h-100 d-flex justify-content-center align-items-center">
                        <div>
                            <i class="bi bi-box-seam display-1 text-muted mb-3 d-block"></i>
                            <h3 class="fw-bold text-uppercase">Mis Pedidos</h3>
                            <p class="text-muted fs-5">Todavía no has realizado ningún pedido.<br> ¡Ve al catálogo a cazar tu próxima prenda favorita!</p>
                            <a href="catalogo.php" class="btn btn-outline-dark rounded-0 px-5 py-2 text-uppercase fw-bold mt-3">Ir al Catálogo</a>
                        </div>
                    </div>
                <?php } else { ?>
                    <h3 class="fw-bold text-uppercase mb-4">Historial de Pedidos</h3>
                    <?php foreach ($listaPedidos as $pedidoTicket) { ?>
                        <div class="card border-dark border-2 rounded-0 mb-4 bg-transparent">
                            <div class="card-header border-bottom border-dark border-2 bg-transparent p-3 d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-uppercase fs-5" style="letter-spacing: 1px;">
                                    Pedido #<?php echo str_pad($pedidoTicket['id'], 5, "0", STR_PAD_LEFT); ?>
                                </span>
                                <span class="fw-bold text-muted">
                                    <?php echo date('d / m / Y', strtotime($pedidoTicket['fecha'])); ?>
                                </span>
                            </div>
                            <div class="card-body p-4">
                                <div class="row mb-4">
                                    <div class="col-6">
                                        <p class="mb-1 text-muted small text-uppercase fw-bold">Estado</p>
                                        <p class="mb-0 fw-bold fs-5 text-uppercase <?php echo ($pedidoTicket['estado'] == 'pendiente') ? 'text-warning' : 'text-success'; ?>">
                                            <?php echo $pedidoTicket['estado']; ?>
                                        </p>
                                    </div>
                                    <div class="col-6 text-end">
                                        <p class="mb-1 text-muted small text-uppercase fw-bold">Total Pagado</p>
                                        <p class="mb-0 fw-bold fs-4"><?php echo number_format($pedidoTicket['total'], 2); ?> €</p>
                                    </div>
                                </div>
                                <h6 class="fw-bold text-uppercase mb-3 border-top border-dark border-2 pt-4">Artículos del pedido:</h6>
                                <ul class="list-unstyled mb-0">
                                    <?php
                                    $lineas = $pedido->obtenerInfoPedido($pedidoTicket["id"]);
                                    if (!empty($lineas)) {
                                        foreach ($lineas as $linea) {
                                    ?>
                                            <li class="d-flex justify-content-between align-items-md-center text-muted small mb-3 text-uppercase fw-bold flex-column flex-md-row gap-2">
                                                <div class="d-flex align-items-center">
                                                    <?php
                                                    $fotoMuestra = !empty($linea['url_imagen']) ? $linea['url_imagen'] : 'public/img/fondo.jpg';
                                                    ?>
                                                    <img src="<?php echo $fotoMuestra; ?>"
                                                        alt="<?php echo $linea['producto_nombre']; ?>"
                                                        class="me-3 border border-dark border-1 flex-shrink-0"
                                                        style="width: 60px; height: 60px; object-fit: cover;">
                                                    <div style="min-width: 0;">
                                                         <span class="text-dark me-1"><?php echo $linea['cantidad']; ?>x</span>
                                                        <span class="d-inline-block text-truncate" style="max-width: 180px; vertical-align: bottom;">
                                                            <?php echo $linea['producto_nombre']; ?>
                                                        </span>
                                                        <span class="d-block mt-1 text-muted" style="font-size: 0.75rem;">
                                                            Talla: <?php echo $linea['talla'] ?? 'N/A'; ?> <span class="d-none d-sm-inline">| Color: <?php echo $linea['color_nombre'] ?? 'N/A'; ?></span>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="text-end text-md-center align-self-end align-self-md-center">
                                                    <span class="text-dark fs-6"><?php echo number_format($linea['precio_unitario'] * $linea['cantidad'], 2); ?> €</span>
                                                </div>
                                            </li>
                                    <?php
                                        };
                                    };
                                    ?>
                                </ul>
                            </div>
                        </div>
                    <?php };  ?>
                <?php };  ?>
            <?php } elseif ($seccion == 'favoritos') { ?>
                <!-- BLOQUE DE FAVORITOS (Mantenido intacto) -->
                <h3 class="fw-bold text-uppercase mb-4">Mis Favoritos</h3>
                <?php if (empty($listaFavoritos)) { ?>
                    <!-- Mensaje vacío Favoritos -->
                    <div class="card border-0 shadow-sm rounded-0 p-5 text-center h-100 d-flex justify-content-center align-items-center bg-light">
                        <div>
                            <i class="bi bi-heart text-muted display-1 mb-3 d-block"></i>
                            <h4 class="fw-bold text-uppercase">Tu lista está vacía</h4>
                            <p class="text-muted">Aún no has guardado ninguna prenda. ¡Descubre nuestro catálogo!</p>
                            <a href="catalogo.php" class="btn btn-outline-dark rounded-0 px-5 py-2 text-uppercase fw-bold mt-3">Ir al Catálogo</a>
                        </div>
                    </div>
                <?php } else { ?>
                    <!-- Lista de Favoritos -->
                    <div class="row g-4">
                        <?php foreach ($listaFavoritos as $prenda) { ?>
                            <div class="col-6 col-md-4 col-lg-3 mb-4">
                                <div class="card product-card border-0 bg-transparent h-100 position-relative d-flex flex-column">
                                    <a href="fichaProducto.php?idPrenda=<?= $prenda['id'] ?>&color=<?= $prenda['color_id'] ?>" class="text-decoration-none text-dark d-block flex-grow-1">
                                        <div class="img-wrapper position-relative overflow-hidden">
                                            <img src="<?= !empty($prenda['url_imagen']) ? $prenda['url_imagen'] : 'public/img/fondo.jpg' ?>" class="card-img-top rounded-0" alt="<?= $prenda['nombre'] ?>" style="height: 380px; object-fit: cover;">
                                        </div>
                                        <div class="card-body text-center px-0 pb-1 mt-2">
                                            <h5 class="card-title text-uppercase fw-bold fs-6 mb-1 text-truncate"><?= $prenda['nombre'] ?></h5>
                                            <p class="card-text mb-2"><?= $prenda['precio'] ?> €</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        <?php }  ?>
                    </div>
                <?php }  ?>
            <?php } elseif ($seccion == 'citas') { ?>
                <!-- BLOQUE DE CITAS (Mantenido intacto) -->
                <h3 class="fw-bold text-uppercase mb-4">Mis Citas</h3>
                <?php if (empty($listaCitas)) { ?>
                    <div class="card border-0 shadow-sm rounded-0 p-5 text-center h-100 d-flex justify-content-center align-items-center bg-light">
                        <div>
                            <i class="bi bi-calendar-x text-muted display-1 mb-3 d-block"></i>
                            <h4 class="fw-bold text-uppercase">No tienes citas programadas</h4>
                            <p class="text-muted">Aún no has reservado ninguna visita a nuestro showroom exclusivo.</p>
                            <a href="citas.php" class="btn btn-outline-dark rounded-0 px-5 py-2 text-uppercase fw-bold mt-3">Reserva ahora</a>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="row g-4">
                        <?php foreach ($listaCitas as $cita) { ?>
                            <div class="col-12 col-md-6">
                                <div class="card border-dark border-1 rounded-0 h-100 bg-transparent">
                                    <div class="card-header border-bottom border-dark border-1 bg-transparent p-3 d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-uppercase fs-6">
                                            <i class="bi bi-calendar-event me-2"></i><?php echo date('d / m / Y', strtotime($cita['fecha_cita'])); ?>
                                        </span>
                                        <span class="fw-bold text-dark bg-light px-3 py-1 border border-dark border-1">
                                            <?php echo date('H:i', strtotime($cita['fecha_cita'])); ?> h
                                        </span>
                                    </div>
                                    <div class="card-body p-4">
                                        <p class="mb-1 text-muted small text-uppercase fw-bold">Motivo de la visita</p>
                                        <p class="mb-4 fw-bold fs-6"><?php echo $cita['motivo']; ?></p>
                                        <div class="d-flex justify-content-between align-items-center border-top pt-3">
                                            <p class="mb-0 text-muted small text-uppercase fw-bold">Estado</p>
                                            <p class="mb-0 fw-bold fs-6 text-uppercase <?php echo ($cita['estado'] == 'pendiente') ? 'text-warning' : (($cita['estado'] == 'cancelada') ? 'text-danger' : 'text-success'); ?>">
                                                <?php echo $cita['estado']; ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php }  ?>
                    </div>
                <?php } ?>
            <?php } ?>
        </section>
    </div>
</main>

<?php include './includes/footer.php'; ?>