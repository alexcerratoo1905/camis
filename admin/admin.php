<?php
session_start();

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/usuario.php";
require_once __DIR__ . "/../models/pedido.php";
require_once __DIR__ . "/../models/producto.php";
require_once __DIR__ . "/../models/look.php";
require_once __DIR__ . "/../includes/auth.php";

redirigirSiNoLogueado("../index.php");

$db = new Database();
$conexion = $db->conectar();
$usu = new Usuario($conexion);

$idUsu = $_SESSION["usuario_id"];
$datosUsu = $usu->obtenerDatosUsu($idUsu);
$pedido = new Pedido($conexion);
$producto  = new Producto($conexion);
$listaProductos = $producto->listarInventarioCompleto();
$listaColeciones = $producto->listarColecciones(true);
$listaUsuarios = $usu->listarUsuarios();

$esSuperAdmin = ($datosUsu["rol_id"] == 1);

$seccion = isset($_GET['seccion']) ? $_GET['seccion'] : 'pedidos';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HERROR | Panel de Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/public/css/style.css">
    <style>
        #drop-zone {
            border: 2px dashed #343a40;
            border-radius: 8px;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        #drop-zone:hover, #drop-zone.dragover {
            background-color: #e9ecef;
            border-color: #0dcaf0;
        }
        .preview-img-container {
            position: relative;
            display: inline-block;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        .preview-img-container img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .crm-thumb-container {
            position: relative;
            display: inline-block;
            margin-right: 8px;
        }
        .crm-thumb {
            width: 55px;
            height: 55px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #444;
            background-color: #fff;
        }
        .btn-borrar-foto {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 10px;
            line-height: 1;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.3);
            text-decoration: none;
        }
        .btn-borrar-foto:hover {
            background: #bd2130;
            color: white;
        }
    </style>
</head>

<body class="admin-body">

    <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow d-md-none" style="height: 60px;">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-5 text-white text-uppercase fw-bold" href="#">
            HERROR <span class="fs-6 fw-normal">Admin</span>
        </a>
        <button class="navbar-toggler position-absolute d-md-none collapsed border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Abrir menú" style="right: 15px; top: 12px;">
            <span class="navbar-toggler-icon"></span>
        </button>
    </header>

    <div class="container-fluid">
        <div class="row">
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar-admin collapse shadow">
                <div class="position-sticky pt-3 pt-md-0">

                    <div class="px-4 mb-4 d-none d-md-block">
                        <h4 class="text-uppercase fw-bold tracking-tighter text-white">HERROR <span class="fs-6 fw-normal">Admin</span></h4>
                    </div>

                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link admin-nav-link <?php echo ($seccion == 'pedidos') ? 'active' : ''; ?>" href="admin.php?seccion=pedidos">
                                <i class="bi bi-bag-check"></i> Pedidos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link admin-nav-link <?php echo ($seccion == 'productos') ? 'active' : ''; ?>" href="admin.php?seccion=productos">
                                <i class="bi bi-box-seam"></i> Productos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link admin-nav-link <?php echo ($seccion == 'colecciones') ? 'active' : ''; ?>" href="admin.php?seccion=colecciones">
                                <i class="bi bi-collection"></i> Categorías
                            </a>
                        </li>

                        <?php if ($esSuperAdmin): ?>
                            <li class="nav-item">
                                <a class="nav-link admin-nav-link <?php echo ($seccion == 'segundaMano') ? 'active' : ''; ?>" href="admin.php?seccion=segundaMano">
                                    <i class="bi bi-arrow-repeat"></i> Segunda mano
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link admin-nav-link <?php echo ($seccion == 'usuarios') ? 'active' : ''; ?>" href="admin.php?seccion=usuarios">
                                    <i class="bi bi-people"></i> Usuarios
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link admin-nav-link <?php echo ($seccion == 'looks') ? 'active' : ''; ?>" href="admin.php?seccion=looks">
                                    <i class="bi bi-palette"></i> Looks
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>

                    <hr class="mx-3 border-secondary">

                    <ul class="nav flex-column mb-2">
                        <li class="nav-item">
                            <a class="nav-link admin-nav-link text-info" href="../index.php">
                                <i class="bi bi-arrow-left-circle"></i> Volver a la Tienda
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link admin-nav-link text-danger" href="../controllers/usuarioController.php?accion=cerrar">
                                <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 admin-content">

                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                    <h1 class="h2 text-uppercase fw-bold"><?php echo ($seccion == 'colecciones') ? 'Categorías' : ucfirst($seccion); ?></h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <span class="badge bg-dark p-2">Usuario: <?php echo $_SESSION['nombre']; ?> <?php echo $esSuperAdmin ? '(SuperAdmin)' : '(Gestor)'; ?></span>
                    </div>
                </div>

                <?php
                if (isset($_GET['mensaje'])) {
                    $msgTexto = "";
                    switch($_GET['mensaje']) {
                        case 'prenda_subida': $msgTexto = "¡La camiseta y sus fotos se han publicado correctamente!"; break;
                        case 'inventario_actualizado': $msgTexto = "¡Los cambios en los productos se han guardado con éxito!"; break;
                        case 'estado_actualizado': $msgTexto = "¡El estado del pedido se ha actualizado!"; break;
                        case 'tracking_enviado': $msgTexto = "¡Correo de seguimiento enviado al cliente!"; break;
                        case 'coleccion_creada': $msgTexto = "¡La nueva categoría se ha creado correctamente!"; break;
                        case 'coleccion_actualizada': $msgTexto = "¡Categoría guardada!"; break;
                        case 'foto_eliminada': $msgTexto = "¡La imagen seleccionada ha sido borrada permanentemente!"; break;
                        case 'fotos_anadidas': $msgTexto = "¡Nuevas fotos añadidas a la galería del producto!"; break;
                    }
                    if ($msgTexto != "") {
                        echo '<div class="alert alert-success alert-dismissible fade show animate__animated animate__fadeIn" role="alert">
                                <i class="bi bi-check-circle-fill me-2"></i> '.$msgTexto.'
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                              </div>';
                    }
                }
                if (isset($_GET['error'])) {
                    echo '<div class="alert alert-danger alert-dismissible fade show animate__animated animate__shakeX" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> Ocurrió un error al procesar la solicitud.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>';
                }
                ?>

                <div class="row">
                    <div class="col-12">
                        <?php
                        switch ($seccion) {
                            case 'pedidos':
                                $listaPedidos = $pedido->listarPedidos();
                        ?>
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h3>Gestión y Seguimiento de Pedidos</h3>
                                    <span class="badge bg-dark fs-6">Total: <?php echo count($listaPedidos); ?> pedidos</span>
                                </div>

                                <div class="table-responsive bg-white p-3 admin-card shadow-sm">
                                    <table class="table admin-table table-hover align-middle">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>ID</th>
                                                <th>Cliente</th>
                                                <th>Fecha</th>
                                                <th>Total</th>
                                                <th>Estado</th>
                                                <th>Acciones de Envío</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($listaPedidos as $p) { 
                                                $datosCliente = $usu->obtenerDatosUsu($p['usuario_id']);
                                                $direccionCompleta = ($datosCliente['direccion'] ?? 'No definida') . ', ' . ($datosCliente['ciudad'] ?? '') . ' (' . ($datosCliente['codigo_postal'] ?? '') . ')';
                                            ?>
                                                <tr>
                                                    <td class="fw-bold">#<?php echo $p['id']; ?></td>
                                                    <td><?php echo $p['nombre_cliente']; ?></td>
                                                    <td><?php echo date('d/m/Y H:i', strtotime($p['fecha'])); ?></td>
                                                    <td><?php echo number_format($p['total'], 2); ?> €</td>
                                                    <td>
                                                        <form action="../controllers/adminController.php" method="POST" class="d-flex gap-2 m-0">
                                                            <input type="hidden" name="accion" value="cambiarEstadoPedido">
                                                            <input type="hidden" name="idPedido" value="<?php echo $p['id']; ?>">
                                                            <select name="nuevoEstado" class="form-select form-select-sm" style="width: auto;">
                                                                <?php
                                                                $estadosPosibles = ['pendiente', 'pagado', 'enviado', 'entregado', 'cancelado'];
                                                                foreach ($estadosPosibles as $estado) {
                                                                    $seleccionado = ($p['estado'] === $estado) ? 'selected' : '';
                                                                ?>
                                                                    <option value="<?php echo $estado; ?>" <?php echo $seleccionado; ?>><?php echo ucfirst($estado); ?></option>
                                                                <?php } ?>
                                                            </select>
                                                            <button type="submit" class="btn btn-sm btn-dark">Actualizar</button>
                                                        </form>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary fw-bold" data-bs-toggle="modal" data-bs-target="#modalTracking<?php echo $p['id']; ?>">
                                                            <i class="bi bi-truck me-1"></i> Enviar Seguimiento
                                                        </button>
                                                    </td>
                                                </tr>

                                                <div class="modal fade" id="modalTracking<?php echo $p['id']; ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content rounded-0 border-dark">
                                                            <div class="modal-header bg-dark text-white rounded-0">
                                                                <h5 class="modal-title text-uppercase fw-bold"><i class="bi bi-envelope me-2"></i>Notificar Envío #<?php echo $p['id']; ?></h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form action="../controllers/enviarTrackingController.php" method="POST">
                                                                <div class="modal-body p-4">
                                                                    <input type="hidden" name="id_pedido" value="<?php echo $p['id']; ?>">
                                                                    <div class="mb-3">
                                                                        <label class="form-label small fw-bold text-muted text-uppercase">Destinatario (Fijo):</label>
                                                                        <input type="text" class="form-control bg-light border-dark" value="<?php echo htmlspecialchars($p['nombre_cliente'] . ' ('.$datosCliente['email'].')'); ?>" readonly>
                                                                        <input type="hidden" name="email_cliente" value="<?php echo $datosCliente['email']; ?>">
                                                                        <input type="hidden" name="nombre_cliente" value="<?php echo $p['nombre_cliente']; ?>">
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label small fw-bold text-muted text-uppercase">Dirección (Fija):</label>
                                                                        <textarea class="form-control bg-light border-dark" rows="2" readonly><?php echo htmlspecialchars($direccionCompleta); ?></textarea>
                                                                    </div>
                                                                    <hr class="border-secondary">
                                                                    <div class="mb-3">
                                                                        <label class="form-label small fw-bold text-uppercase">Número de Seguimiento (Tracking):</label>
                                                                        <input type="text" name="tracking_number" class="form-control border-dark" placeholder="Ej: 1Z999AA10123456784" required>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label small fw-bold text-uppercase">Enlace web (URL):</label>
                                                                        <input type="url" name="tracking_url" class="form-control border-dark" placeholder="https://www.correos.es/tracking" required>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer bg-light border-top rounded-0">
                                                                    <button type="button" class="btn btn-secondary rounded-0 btn-sm fw-bold" data-bs-dismiss="modal">Cancelar</button>
                                                                    <button type="submit" class="btn btn-dark rounded-0 btn-sm fw-bold"><i class="bi bi-send me-1"></i> Disparar Correo</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                        <?php
                                break;

                            case 'productos':
                                $prod = new Producto($db->conectar());

                                $productosPorPagina = 5;
                                $paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
                                if ($paginaActual < 1) $paginaActual = 1;

                                $totalProductos = $prod->contarProductosPorTipo(false);
                                $totalPaginas = ceil($totalProductos / $productosPorPagina);
                                $offset = ($paginaActual - 1) * $productosPorPagina;

                                $listaInventario = $prod->listarProductosPaginados(false, $productosPorPagina, $offset);

                                $productosAgrupados = [];
                                if (!empty($listaInventario)) {
                                    foreach ($listaInventario as $item) {
                                        $pId = $item['prenda_id'];
                                        if (!isset($productosAgrupados[$pId])) {
                                            $productosAgrupados[$pId] = [
                                                'nombre' => $item['nombre'],
                                                'precio' => $item['precio'],
                                                'rebaja' => $item['rebaja'],
                                                'activo' => $item['activo'],
                                                'coleccion_id' => $item['coleccion_id'],
                                                'es_segunda_mano' => $item['es_segunda_mano']
                                            ];
                                        }
                                    }
                                }
                        ?>
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div>
                                        <h3 class="fw-bold m-0 text-uppercase">Gestión de Inventario</h3>
                                        <small class="text-muted">Mostrando página <?php echo $paginaActual; ?> de <?php echo $totalPaginas; ?> (<?php echo $totalProductos; ?> productos totales)</small>
                                    </div>
                                    <button class="btn btn-admin-black px-3 py-2" type="button" data-bs-toggle="collapse" data-bs-target="#formNuevaPrenda">
                                        <i class="bi bi-plus-lg me-2"></i> Añadir Camiseta
                                    </button>
                                </div>

                                <div class="collapse mb-4 mt-3" id="formNuevaPrenda">
                                    <div class="card card-body admin-card border-0 shadow-sm bg-light">
                                        <h5 class="fw-bold mb-3 text-uppercase"><i class="bi bi-box-seam me-2"></i>Añadir Nueva Camiseta al Catálogo</h5>
                                        
                                        <form action="../controllers/adminController.php" method="POST" enctype="multipart/form-data" class="row g-3" id="formularioSubida">
                                            <input type="hidden" name="accion" value="crearPrendaTienda">

                                            <div class="col-md-4">
                                                <label class="fw-bold small">Nombre del Producto:</label>
                                                <input type="text" name="nombre" class="form-control border-dark" placeholder="Ej: Real Madrid 24/25" required>
                                            </div>
                                            <div class="col-6 col-md-2">
                                                <label class="fw-bold small">Precio Base (€):</label>
                                                <input type="number" step="0.01" name="precio" class="form-control border-dark" value="19.00" required>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <label class="fw-bold small">Equipación:</label>
                                                <select name="equipacion" class="form-select border-dark" required>
                                                    <option value="Local">Local</option>
                                                    <option value="Visitante">Visitante</option>
                                                    <option value="Tercera">Tercera Equipación</option>
                                                </select>
                                            </div>
                                            <div class="col-12 col-md-3">
                                                <label class="fw-bold small">Categoría (Liga):</label>
                                                <select name="coleccion_id" class="form-select border-dark" required>
                                                    <option value="">Selecciona Liga...</option>
                                                    <?php foreach ($listaColeciones as $c) { ?>
                                                        <option value="<?php echo $c['id']; ?>"><?php echo $c['nombre']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="fw-bold small">Descripción (Opcional):</label>
                                                <textarea name="descripcion" class="form-control border-dark" rows="1"></textarea>
                                            </div>

                                            <div class="col-md-12 mt-3">
                                                <label class="fw-bold small mb-2"><i class="bi bi-images me-1"></i> Fotos de la Camiseta:</label>
                                                <div id="drop-zone" class="p-5 text-center text-muted">
                                                    <i class="bi bi-cloud-arrow-up display-4"></i>
                                                    <h5 class="mt-2">Haz Clic, Arrastra o pega (Ctrl+V) tus fotos aquí</h5>
                                                    <p class="small mb-0">Puedes ir a Yupoo, hacer "Copiar Imagen" y darle a Ctrl+V directamente en esta ventana.</p>
                                                </div>
                                                <div id="preview-container" class="mt-3"></div>
                                                <input type="file" name="imagenes[]" id="file-input" class="d-none" accept="image/*" multiple required>
                                            </div>

                                            <div class="col-12 text-end mt-4">
                                                <button type="submit" class="btn btn-admin-black px-5 py-3 shadow-lg fw-bold w-100 w-md-auto"><i class="bi bi-cloud-arrow-up me-2"></i> PUBLICAR CAMISETA</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <form action="../controllers/adminController.php" method="POST">
                                    <input type="hidden" name="accion" value="actualizarInventarioMasivo">
                                    <input type="hidden" name="pagina_retorno" value="<?php echo $paginaActual; ?>">

                                    <?php if (empty($productosAgrupados)) { ?>
                                        <div class="alert alert-secondary text-center py-5">No se han encontrado productos en esta página.</div>
                                    <?php } else { ?>
                                        <?php foreach ($productosAgrupados as $id => $datos) { 
                                            if ($datos['es_segunda_mano'] == 1) continue;

                                            // CONSULTA DE SEGURIDAD PARA SACAR LAS FOTOS DIRECTAMENTE DE LA BD
                                            $stmtFotos = $conexion->prepare("SELECT id, url_imagen FROM imagenes_productos WHERE producto_id = ?");
                                            $stmtFotos->execute([$id]);
                                            $fotosProducto = $stmtFotos->fetchAll(PDO::FETCH_ASSOC);

                                            // CONSULTA DE SEGURIDAD PARA TRAER LA DESCRIPCIÓN EXACTA DE LA BD
                                            $stmtDesc = $conexion->prepare("SELECT descripcion FROM productos WHERE id = ?");
                                            $stmtDesc->execute([$id]);
                                            $descReal = $stmtDesc->fetchColumn();
                                        ?>
                                            <div class="card mb-4 border-0 shadow-sm admin-card" style="border-left: 6px solid #0dcaf0;">
                                                <div class="card-header bg-dark text-white py-3">
                                                    <div class="row align-items-center g-3">
                                                        <div class="col-12 col-lg-3">
                                                            <div class="d-flex align-items-center gap-1">
                                                                <span class="text-secondary fw-bold small">#<?php echo $id; ?></span>
                                                                <input type="text" name="nombre[<?php echo $id; ?>]" value="<?php echo htmlspecialchars($datos['nombre']); ?>" class="form-control form-control-sm border-0 bg-secondary text-white fw-bold text-uppercase w-100" style="letter-spacing: 0.5px;" required>
                                                            </div>
                                                            
                                                            <div class="mt-2 d-flex align-items-center flex-wrap gap-1">
                                                                <?php foreach ($fotosProducto as $ft) { 
                                                                    if(empty($ft['url_imagen'])) continue;
                                                                ?>
                                                                    <div class="crm-thumb-container">
                                                                        <img src="../<?php echo htmlspecialchars($ft['url_imagen']); ?>" class="crm-thumb" alt="Foto">
                                                                        <a href="../controllers/adminController.php?accion=borrarFotoEspecifica&id_foto=<?php echo $ft['id']; ?>&p_id=<?php echo $id; ?>&pag=<?php echo $paginaActual; ?>" class="btn-borrar-foto" onclick="return confirm('¿Seguro que quieres eliminar esta imagen del catálogo?');">×</a>
                                                                    </div>
                                                                <?php } ?>
                                                                
                                                                <button type="button" class="btn btn-sm btn-outline-info text-white border-secondary px-2 py-1 small" onclick="document.getElementById('add-foto-input-<?php echo $id; ?>').click();" title="Añadir más fotos a este producto">
                                                                    <i class="bi bi-plus-lg"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="col-6 col-md-3 col-lg-2">
                                                            <label class="d-md-none small text-muted d-block mb-1">Liga</label>
                                                            <select name="coleccion[<?php echo $id; ?>]" class="form-select form-select-sm border-0 bg-light text-dark fw-bold w-100">
                                                                <option value="">Sin Liga</option>
                                                                <?php foreach ($listaColeciones as $col) { 
                                                                    $seleccionado = ($col['id'] == $datos['coleccion_id']) ? 'selected' : '';
                                                                ?>
                                                                    <option value="<?php echo $col['id']; ?>" <?php echo $seleccionado; ?>><?php echo htmlspecialchars($col['nombre']); ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-6 col-md-3 col-lg-2">
                                                            <label class="d-md-none small text-muted d-block mb-1">Precio</label>
                                                            <div class="input-group input-group-sm">
                                                                <input type="number" step="0.01" name="precio[<?php echo $id; ?>]" value="<?php echo $datos['precio']; ?>" class="form-control text-center fw-bold border-0 bg-light text-dark">
                                                                <span class="input-group-text bg-light border-0 fw-bold text-dark">€</span>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="col-6 col-md-3 col-lg-2">
                                                            <label class="d-md-none small text-muted d-block mb-1">Rebaja</label>
                                                            <div class="input-group input-group-sm">
                                                                <span class="input-group-text bg-secondary text-white border-0 small d-none d-md-block">%</span>
                                                                <input type="number" name="rebaja[<?php echo $id; ?>]" value="<?php echo $datos['rebaja']; ?>" class="form-control text-center fw-bold" min="0" max="100">
                                                            </div>
                                                        </div>

                                                        <div class="col-6 col-md-3 col-lg-3">
                                                            <label class="d-md-none small text-muted d-block mb-1">Estado</label>
                                                            <select name="activo[<?php echo $id; ?>]" class="form-select form-select-sm fw-bold border-0 <?php echo ($datos['activo'] == 1 ? 'text-success' : 'text-danger'); ?>">
                                                                <option value="1" <?php echo ($datos['activo'] == 1 ? 'selected' : ''); ?>>ACTIVO</option>
                                                                <option value="0" <?php echo ($datos['activo'] == 0 ? 'selected' : ''); ?>>OCULTO</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row mt-3 pt-2 border-top border-secondary">
                                                        <div class="col-12">
                                                            <div class="input-group input-group-sm">
                                                                <span class="input-group-text bg-secondary text-white border-0 small font-monospace">INFO</span>
                                                                <input type="text" name="descripcion[<?php echo $id; ?>]" value="<?php echo htmlspecialchars($descReal ?? ''); ?>" class="form-control bg-dark text-white border-0 small" placeholder="Descripción breve de la camiseta (Ej: Escudo bordado, parches de liga oficiales...)">
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>

                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-5 mb-5 pt-3 border-top gap-3">
                                        <nav aria-label="Paginación de inventario">
                                            <ul class="pagination mb-0 shadow-sm">
                                                <?php
                                                $disabledPrev = ($paginaActual <= 1) ? 'disabled' : '';
                                                $urlPrev = 'admin.php?seccion=productos&pagina=' . ($paginaActual - 1);
                                                ?>
                                                <li class="page-item <?php echo $disabledPrev; ?>"><a class="page-link text-dark" href="<?php echo $urlPrev; ?>">Anterior</a></li>

                                                <?php
                                                for ($i = 1; $i <= $totalPaginas; $i++) {
                                                    $activa = ($i == $paginaActual) ? 'active bg-dark border-dark text-white' : 'text-dark';
                                                ?>
                                                    <li class="page-item"><a class="page-link <?php echo $activa; ?>" href="admin.php?seccion=productos&pagina=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                                                <?php } ?>

                                                <?php
                                                $disabledNext = ($paginaActual >= $totalPaginas) ? 'disabled' : '';
                                                $urlNext = 'admin.php?seccion=productos&pagina=' . ($paginaActual + 1);
                                                ?>
                                                <li class="page-item <?php echo $disabledNext; ?>"><a class="page-link text-dark" href="<?php echo $urlNext; ?>">Siguiente</a></li>
                                            </ul>
                                        </nav>
                                        
                                        <button type="submit" class="btn btn-admin-black px-5 py-3 shadow-lg fw-bold w-100 w-md-auto position-sticky bottom-0 z-3" style="bottom: 15px;"><i class="bi bi-save me-2"></i> GUARDAR CAMBIOS</button>
                                    </div>
                                </form>

                                <?php foreach ($productosAgrupados as $id => $datos) { 
                                    if ($datos['es_segunda_mano'] == 1) continue;
                                ?>
                                    <form id="form-add-foto-<?php echo $id; ?>" action="../controllers/adminController.php" method="POST" enctype="multipart/form-data" class="d-none">
                                        <input type="hidden" name="accion" value="anadirFotosGaleriaExistente">
                                        <input type="hidden" name="producto_id" value="<?php echo $id; ?>">
                                        <input type="hidden" name="pagina_retorno" value="<?php echo $paginaActual; ?>">
                                        <input type="file" id="add-foto-input-<?php echo $id; ?>" name="imagenes[]" onchange="document.getElementById('form-add-foto-<?php echo $id; ?>').submit();" accept="image/*" multiple>
                                    </form>
                                <?php } ?>

                        <?php
                                break;
                            case 'colecciones':
                                $todasLasColecciones = $producto->listarColecciones(true);
                        ?>
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h3>Gestión de Ligas / Categorías</h3>
                                    <button class="btn btn-admin-black px-3 py-2" type="button" data-bs-toggle="collapse" data-bs-target="#formNuevaColeccion">
                                        <i class="bi bi-plus-lg me-2"></i> Nueva Categoría
                                    </button>
                                </div>

                                <div class="collapse mb-4" id="formNuevaColeccion">
                                    <div class="card card-body admin-card border-0 shadow-sm bg-light">
                                        <form action="../controllers/adminController.php" method="POST" class="row g-3 align-items-end">
                                            <input type="hidden" name="accion" value="crearColeccion">
                                            <div class="col-12 col-md-4">
                                                <label class="fw-bold mb-1 small text-uppercase">Nombre de la Categoría:</label>
                                                <input type="text" name="nombre_coleccion" class="form-control border-dark" placeholder="Ej: Premier League" required>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label class="fw-bold mb-1 small text-uppercase">Descripción:</label>
                                                <textarea name="descripcion_coleccion" class="form-control border-dark" rows="1"></textarea>
                                            </div>
                                            <div class="col-12 col-md-2">
                                                <button type="submit" class="btn btn-dark w-100 fw-bold">Crear Categoría</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <div class="table-responsive bg-white p-3 admin-card shadow-sm">
                                    <table class="table admin-table table-hover align-middle">
                                        <thead class="table-dark text-center">
                                            <tr>
                                                <th>ID</th>
                                                <th>Nombre Categoría</th>
                                                <th>Descripción</th>
                                                <th>Estado</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($todasLasColecciones as $col) { ?>
                                                <tr>
                                                    <form action="../controllers/adminController.php" method="POST">
                                                        <input type="hidden" name="accion" value="actualizarColeccion">
                                                        <input type="hidden" name="id_coleccion" value="<?php echo $col['id']; ?>">
                                                        <td class="text-center text-secondary fw-bold">#<?php echo $col['id']; ?></td>
                                                        <td><input type="text" name="nombre" value="<?php echo htmlspecialchars($col['nombre']); ?>" class="form-control form-control-sm fw-bold border-dark"></td>
                                                        <td><textarea name="descripcion" class="form-control form-control-sm border-dark" rows="1"><?php echo htmlspecialchars($col['descripcion'] ?? ''); ?></textarea></td>
                                                        <td>
                                                            <select name="nuevo_estado" class="form-select form-select-sm border-dark">
                                                                <option value="1" <?php echo ($col['activa'] == 1 ? 'selected' : ''); ?>>Activa</option>
                                                                <option value="2" <?php echo ($col['activa'] == 2 ? 'selected' : ''); ?>>Inactiva</option>
                                                            </select>
                                                        </td>
                                                        <td class="text-center"><button type="submit" class="btn btn-sm btn-dark"><i class="bi bi-check-lg"></i></button></td>
                                                    </form>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                        <?php
                                break;
                            
                            case 'segundaMano':
                            case 'usuarios':
                            case 'looks':
                                if (!$esSuperAdmin) echo "<div class='alert alert-danger'>No tienes permisos.</div>";
                                break;
                        }
                        ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const dropZone = document.getElementById('drop-zone');
        if (!dropZone) return;

        const fileInput = document.getElementById('file-input');
        const previewContainer = document.getElementById('preview-container');
        const dataTransfer = new DataTransfer();

        dropZone.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', (e) => procesarArchivos(e.target.files));
        
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('bg-secondary', 'text-white');
        });
        dropZone.addEventListener('dragleave', () => dropZone.classList.remove('bg-secondary', 'text-white'));
        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('bg-secondary', 'text-white');
            if (e.dataTransfer.files.length > 0) procesarArchivos(e.dataTransfer.files);
        });

        window.addEventListener('paste', (e) => {
            if (e.clipboardData && e.clipboardData.files.length > 0) {
                if(e.clipboardData.files[0].type.startsWith('image/')) {
                    e.preventDefault(); 
                    procesarArchivos(e.clipboardData.files);
                }
            }
        });

        function procesarArchivos(files) {
            for (let i = 0; i < files.length; i++) {
                let file = files[i];
                if (!file.type.startsWith('image/')) continue;

                let safeFile = file;
                if (file.name === "image.png" || file.name === "image.jpg") {
                    const extension = file.type.split('/')[1];
                    const randomName = "captura_" + Date.now() + "_" + Math.floor(Math.random() * 1000) + "." + extension;
                    safeFile = new File([file], randomName, { type: file.type });
                }
                dataTransfer.items.add(safeFile);
                dibujarMiniatura(safeFile);
            }
            fileInput.files = dataTransfer.files;
            dropZone.querySelector('h5').innerText = dataTransfer.files.length + " fotos locales listas";
        }

        function dibujarMiniatura(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const container = document.createElement('div');
                container.className = 'preview-img-container';
                const img = document.createElement('img');
                img.src = e.target.result;
                container.appendChild(img);
                previewContainer.appendChild(container);
            };
            reader.readAsDataURL(file);
        }
    });
    </script>
</body>
</html>