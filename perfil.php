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
                                <input type="text" class="form-control rounded-0" name="nombre" value="<?php echo isset($datosUsu['nombre']) ? $datosUsu['nombre'] : ''; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small fw-bold text-uppercase">Apellidos</label>
                                <input type="text" class="form-control rounded-0" name="apellidos" value="<?php echo isset($datosUsu['apellidos']) ? $datosUsu['apellidos'] : ''; ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small fw-bold text-uppercase">Email (No se puede cambiar)</label>
                                <input type="email" class="form-control rounded-0 text-muted" value="<?php echo isset($datosUsu['email']) ? $datosUsu['email'] : ''; ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label text-muted small fw-bold text-uppercase">Teléfono</label>
                                <input type="text" class="form-control rounded-0" name="telefono" value="<?php echo isset($datosUsu['telefono']) ? $datosUsu['telefono'] : ''; ?>">
                            </div>
                        </div>


                        <h3 class="fw-bold text-uppercase mb-4 mt-3 border-top pt-4">Dirección de Envío</h3>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label text-muted small fw-bold text-uppercase">Dirección Completa (Calle, número, piso)</label>
                                <input type="text" class="form-control rounded-0" name="direccion" value="<?php echo isset($datosUsu['direccion']) ? $datosUsu['direccion'] : ''; ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label text-muted small fw-bold text-uppercase">Ciudad</label>
                                <input type="text" class="form-control rounded-0" name="ciudad" value="<?php echo isset($datosUsu['ciudad']) ? $datosUsu['ciudad'] : ''; ?>">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label text-muted small fw-bold text-uppercase">Código Postal</label>
                                <input type="text" class="form-control rounded-0" name="codigoPostal" value="<?php echo isset($datosUsu['codigo_postal']) ? $datosUsu['codigo_postal'] : ''; ?>">
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

                <?php if (empty($listaPedidos)) { ?>
                    <div class="card border-0 shadow-sm rounded-0 p-5 text-center h-100 d-flex justify-content-center align-items-center">
                        <div>
                            <i class="bi bi-box-seam display-1 text-muted mb-3 d-block"></i>
                            <h3 class="fw-bold text-uppercase">Mis Pedidos</h3>
                            <p class="text-muted fs-5">Todavía no has realizado ningún pedido.<br>¡Ve al catálogo a cazar tu próxima prenda favorita!</p>
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

                <h3 class="fw-bold text-uppercase mb-4">Mis Favoritos</h3>

                <?php if (empty($listaFavoritos)) { ?>
                    <div class="card border-0 shadow-sm rounded-0 p-5 text-center h-100 d-flex justify-content-center align-items-center bg-light">
                        <div>
                            <i class="bi bi-heart text-muted display-1 mb-3 d-block"></i>
                            <h4 class="fw-bold text-uppercase">Tu lista está vacía</h4>
                            <p class="text-muted">Aún no has guardado ninguna prenda. ¡Descubre nuestro catálogo!</p>
                            <a href="catalogo.php" class="btn btn-outline-dark rounded-0 px-5 py-2 text-uppercase fw-bold mt-3">Ir al Catálogo</a>
                        </div>
                    </div>
                <?php } else { ?>

                    <div class="row g-4">

                        <?php foreach ($listaFavoritos as $prenda) { ?>
                            <div class="col-6 col-md-4 col-lg-3 mb-4">
                                <div class="card product-card border-0 bg-transparent h-100 position-relative d-flex flex-column">

                                    <a href="fichaProducto.php?idPrenda=<?= $prenda['id'] ?>&color=<?= $prenda['color_id'] ?>" class="text-decoration-none text-dark d-block flex-grow-1">

                                        <div class="img-wrapper position-relative overflow-hidden">
                                            <img src="<?= !empty($prenda['url_imagen']) ? $prenda['url_imagen'] : 'public/img/fondo.jpg' ?>" class="card-img-top rounded-0" alt="<?= $prenda['nombre'] ?>" style="height: 380px; object-fit: cover;">

                                            <div id="overlay-tallas-<?= $prenda['id'] ?>" class="overlay-tallas d-none position-absolute bottom-0 start-0 w-100 bg-white bg-opacity-75 p-2 text-center" style="z-index: 20;" onclick="event.preventDefault();">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="small fw-bold text-uppercase" style="letter-spacing: 1px; font-size: 0.7rem;">Talla</span>
                                                    <button type="button" class="btn-close" style="font-size: 0.6rem;" onclick="cerrarOverlayTallas(event, <?= $prenda['id'] ?>)"></button>
                                                </div>
                                                <div id="contenedor-botones-<?= $prenda['id'] ?>" class="d-flex justify-content-center flex-wrap gap-1">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card-body text-center px-0 pb-1 mt-2">
                                            <h5 class="card-title text-uppercase fw-bold fs-6 mb-1 text-truncate"><?= $prenda['nombre'] ?></h5>
                                            <p class="card-text mb-2"><?= $prenda['precio'] ?> €</p>
                                        </div>
                                    </a>

                                    <div class="d-flex align-items-center justify-content-between gap-1 mt-auto px-1 pt-2">
                                        <button type="button" class="btn btn-principal rounded-0 flex-grow-1 text-uppercase fw-bold py-1 px-0"
                                            style="height: 40px; font-size: 0.75rem; letter-spacing: 1px;"
                                            onclick="abrirOverlayTallas(event, <?= $prenda['id'] ?>, <?= $prenda['color_id'] ?>)">
                                            Añadir
                                        </button>

                                        <button type="button" class="btn btn-toggle-favorito btn-favorito-custom btn-favorito-std d-flex justify-content-center align-items-center rounded-0 m-0"
                                            style="height: 40px; width: 40px; padding: 0;"
                                            data-id="<?= $prenda['id'] ?>"
                                            data-color="<?= $prenda['color_id'] ?>">
                                            <i class="bi bi-heart-fill"></i>
                                        </button>
                                    </div>

                                </div>
                            </div>
                        <?php }  ?>
                    </div>
                <?php }  ?>


            <?php } elseif ($seccion == 'citas') { ?>

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

            <?php } elseif ($seccion == 'prendas') { ?>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="fw-bold text-uppercase mb-0">Mi Armario</h3>
                    <a href="segundaMano.php" class="btn btn-dark rounded-0 px-4 py-2 text-uppercase fw-bold small">
                        <i class="bi bi-plus-lg me-1"></i> Subir Prenda
                    </a>
                </div>

                <?php if (empty($listaPrendasUsu)) { ?>
                    <div class="card border-0 shadow-sm rounded-0 p-5 text-center h-100 d-flex justify-content-center align-items-center bg-light">
                        <div>
                            <i class="bi bi-tags text-muted display-1 mb-3 d-block"></i>
                            <h4 class="fw-bold text-uppercase">Tu armario está vacío</h4>
                            <p class="text-muted">Aún no has subido ninguna prenda para darle una segunda vida.</p>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="row g-4">
                        <?php foreach ($listaPrendasUsu as $prenda) { ?>
                            <div class="col-12 col-md-6 col-xl-4">
                                <div class="card border-dark border-1 rounded-0 h-100 bg-transparent">

                                    <div class="position-relative overflow-hidden border-bottom border-dark border-1" style="height: 250px;">
                                        <img src="<?php echo !empty($prenda['url_imagen']) ? $prenda['url_imagen'] : 'public/img/fondo.jpg'; ?>"
                                            class="w-100 h-100" style="object-fit: cover;" alt="<?php echo $prenda['nombre']; ?>">

                                        <?php
                                        $colorEstado = 'bg-warning text-dark';
                                        if ($prenda['estado_revision'] == 'Aprobado') $colorEstado = 'bg-success text-white';
                                        if ($prenda['estado_revision'] == 'Rechazado') $colorEstado = 'bg-danger text-white';
                                        ?>
                                        <span class="position-absolute top-0 start-0 m-2 badge <?php echo $colorEstado; ?> rounded-0 fw-bold text-uppercase px-2 py-1 border border-dark border-1" style="font-size: 0.7rem;">
                                            <?php echo $prenda['estado_revision']; ?>
                                        </span>
                                    </div>

                                    <div class="card-body p-3 d-flex flex-column">
                                        <h6 class="fw-bold text-uppercase mb-1 text-truncate"><?php echo $prenda['nombre']; ?></h6>
                                        <p class="text-muted mb-3 fs-5 fw-bold"><?php echo number_format($prenda['precio'], 2); ?> €</p>

                                        <div class="mt-auto pt-3 border-top border-dark border-1">
                                            <button type="button" class="btn btn-outline-dark rounded-0 w-100 fw-bold text-uppercase text-center"
                                                style="font-size: 0.8rem; letter-spacing: 1px;"
                                                data-bs-toggle="modal" data-bs-target="#modalEditar-<?php echo $prenda['id']; ?>"> Editar Prenda
                                            </button>
                                        </div>

                                        <div class="modal fade text-start" id="modalEditar-<?php echo $prenda['id']; ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                <div class="modal-content rounded-0 border-0 shadow">

                                                    <div class="modal-header border-bottom-0 pb-0 mt-3 px-4">
                                                        <h5 class="modal-title fw-bold text-uppercase fs-4">Editar Prenda</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>

                                                    <div class="modal-body px-4 pb-4">
                                                        <form action="controllers/editarPrendaController.php" method="POST" enctype="multipart/form-data">
                                                            <input type="hidden" name="idPrenda" value="<?php echo $prenda['id']; ?>">

                                                            <div class="row">
                                                                <div class="col-md-8 mb-3">
                                                                    <label class="form-label fw-bold small text-uppercase text-muted">Título de la prenda</label>
                                                                    <input type="text" class="form-control rounded-0 p-2" name="nombrePrenda" value="<?php echo $prenda['nombre']; ?>" required>
                                                                </div>
                                                                <div class="col-md-4 mb-3">
                                                                    <label class="form-label fw-bold small text-uppercase text-muted">Precio (€)</label>
                                                                    <input type="number" step="0.01" class="form-control rounded-0 p-2" name="precioPrenda" value="<?php echo $prenda['precio']; ?>" required>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-md-4 mb-3">
                                                                    <label class="form-label fw-bold small text-uppercase text-muted">Talla</label>
                                                                    <select class="form-select rounded-0 p-2" name="tallaPrenda" required>
                                                                        <?php foreach ($listaTallas as $talla) { ?>
                                                                            <option value="<?php echo $talla['talla']; ?>" <?php echo ($prenda['talla'] == $talla['talla']) ? 'selected' : ''; ?>>
                                                                                <?php echo $talla['talla']; ?>
                                                                            </option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-4 mb-3">
                                                                    <label class="form-label fw-bold small text-uppercase text-muted">Color</label>
                                                                    <select class="form-select rounded-0 p-2" name="colorPrenda" required>
                                                                        <?php foreach ($listaColores as $color) { ?>
                                                                            <option value="<?php echo $color['id']; ?>" <?php echo ($prenda['color_id'] == $color['id']) ? 'selected' : ''; ?>>
                                                                                <?php echo $color['nombre']; ?>
                                                                            </option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-4 mb-3">
                                                                    <label class="form-label fw-bold small text-uppercase text-muted">Tipo Prenda</label>
                                                                    <select class="form-select rounded-0 p-2" name="tipoPrenda" required>
                                                                        <?php foreach ($listaTipoPrenda as $tipo) { ?>
                                                                            <option value="<?php echo $tipo['id']; ?>" <?php echo ($prenda['tipo_id'] == $tipo['id']) ? 'selected' : ''; ?>>
                                                                                <?php echo $tipo['nombre']; ?>
                                                                            </option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="mb-3 mt-2 border-top pt-3">
                                                                <label class="form-label fw-bold small text-uppercase text-muted">Fotos actuales (Marca la casilla para eliminar)</label>
                                                                <div class="d-flex flex-wrap gap-2">
                                                                    <?php
                                                                    $fotosPrenda = $producto->obtenerImagenesProducto($prenda['id']);
                                                                    foreach ($fotosPrenda as $foto) {
                                                                    ?>
                                                                        <div class="position-relative border border-2 border-dark p-1" style="width: 100px; height: 100px;">
                                                                            <img src="<?php echo $foto['url_imagen']; ?>" class="w-100 h-100" style="object-fit: cover;" alt="Foto prenda">
                                                                            <div class="form-check position-absolute top-0 start-0 m-1 bg-white border border-dark px-2 py-1 shadow-sm">
                                                                                <input class="form-check-input m-0" type="checkbox" name="fotosABorrar[]" value="<?php echo $foto['id']; ?>" title="Marcar para borrar">
                                                                            </div>
                                                                        </div>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>

                                                            <div class="mb-4 mt-3 border-top pt-3">
                                                                <label class="form-label fw-bold small text-uppercase text-muted">Añadir más fotos</label>
                                                                <input class="form-control rounded-0" type="file" name="fotosNuevas[]" accept="image/*" multiple>
                                                            </div>

                                                            <div class="d-grid mt-4">
                                                                <button type="submit" class="btn btn-dark rounded-0 py-3 text-uppercase fw-bold" style="letter-spacing: 1px;">
                                                                    Guardar Cambios
                                                                </button>
                                                            </div>

                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>

            <?php } ?>

        </section>
    </div>
</main>

<?php include './includes/footer.php'; ?>