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
$producto = new Producto($conexion);
$listaColeciones = $producto->listarColecciones(true);
$listaUsuarios = $usu->listarUsuarios();

$esSuperAdmin = ($datosUsu["rol_id"] == 1);

$seccion = isset($_GET['seccion']) ? $_GET['seccion'] : 'pedidos';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>HERROR | Panel de Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/public/css/style.css">
    <style>
        body, html { overflow-x: hidden; }
        #drop-zone { border: 2px dashed #343a40; border-radius: 8px; background-color: #f8f9fa; transition: all 0.3s ease; cursor: pointer; }
        #drop-zone:hover, #drop-zone.dragover { background-color: #e9ecef; border-color: #0dcaf0; }
        .preview-img-container { position: relative; display: inline-block; margin-right: 10px; margin-bottom: 10px; }
        .preview-img-container img { width: 100px; height: 100px; object-fit: cover; border-radius: 5px; border: 1px solid #ccc; }
        
        .crm-thumb-container { position: relative; display: inline-block; margin-right: 8px; margin-bottom: 8px; }
        .crm-thumb { width: 55px; height: 55px; object-fit: cover; border-radius: 4px; border: 1px solid #ccc; background-color: #fff; }
        .btn-borrar-foto { position: absolute; top: -5px; right: -5px; background: #dc3545; color: white; border: none; border-radius: 50%; width: 22px; height: 22px; font-size: 14px; line-height: 1; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.3); text-decoration: none; z-index: 10; }
        .btn-borrar-foto:hover { background: #bd2130; color: white; }
        
        .nav-tabs-scroll { display: flex; flex-wrap: nowrap; overflow-x: auto; overflow-y: hidden; -webkit-overflow-scrolling: touch; border-bottom: 2px solid #dee2e6; margin-bottom: 15px; }
        .nav-tabs-scroll::-webkit-scrollbar { height: 4px; }
        .nav-tabs-scroll::-webkit-scrollbar-thumb { background: #ccc; border-radius: 4px; }
        .nav-tabs .nav-link { color: #495057 !important; border-radius: 0; padding: 0.5rem 0.8rem; font-size: 0.85rem; font-weight: 600; border: none; white-space: nowrap; }
        .nav-tabs .nav-link:hover { color: #000 !important; background-color: #f8f9fa; }
        .nav-tabs .nav-link.active { color: #000 !important; border-bottom: 3px solid #000; font-weight: 800; background-color: #f8f9fa !important; }
        
        .btn-flotante-guardar { position: fixed; bottom: 30px; right: 30px; z-index: 1040; box-shadow: 0 10px 25px rgba(0,0,0,0.3); transition: transform 0.2s; }
        .btn-flotante-guardar:hover { transform: scale(1.05); }
        .btn-flotante-movil { position: fixed; bottom: 0; left: 0; width: 100%; padding: 15px; background: white; border-top: 1px solid #ddd; z-index: 1040; box-shadow: 0 -5px 15px rgba(0,0,0,0.1); }
        .espacio-movil { padding-bottom: 100px; }
    </style>
</head>

<body class="admin-body">

    <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow d-md-none" style="height: 60px;">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-5 text-white text-uppercase fw-bold" href="#">
            HERROR <span class="fs-6 fw-normal">Admin</span>
        </a>
        <button class="navbar-toggler position-absolute d-md-none collapsed border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
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
                        <li class="nav-item"><a class="nav-link admin-nav-link text-info" href="../index.php"><i class="bi bi-arrow-left-circle"></i> Volver a la Tienda</a></li>
                        <li class="nav-item"><a class="nav-link admin-nav-link text-danger" href="../controllers/usuarioController.php?accion=cerrar"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a></li>
                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 admin-content espacio-movil">

                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                    <h1 class="h2 text-uppercase fw-bold"><?php echo ($seccion == 'colecciones') ? 'Categorías' : ucfirst($seccion); ?></h1>
                </div>

                <?php
                if (isset($_GET['mensaje'])) {
                    $msgTexto = "";
                    switch($_GET['mensaje']) {
                        case 'prenda_subida': $msgTexto = "¡La camiseta se ha publicado correctamente!"; break;
                        case 'inventario_actualizado': $msgTexto = "¡Los cambios en los productos se han guardado con éxito!"; break;
                        case 'estado_actualizado': $msgTexto = "¡El estado del pedido se ha actualizado!"; break;
                        case 'foto_eliminada': $msgTexto = "¡La imagen ha sido borrada permanentemente!"; break;
                        case 'fotos_anadidas': $msgTexto = "¡Nuevas fotos añadidas a la galería!"; break;
                        case 'variante_anadida': $msgTexto = "¡Nueva equipación añadida al producto con éxito!"; break;
                        case 'variante_borrada': $msgTexto = "¡La variante (equipación) ha sido eliminada permanentemente!"; break;
                        case 'coleccion_creada': $msgTexto = "¡La nueva categoría se ha creado correctamente!"; break;
                        case 'coleccion_actualizada': $msgTexto = "¡Categoría, precios y descuentos guardados con éxito!"; break;
                        case 'coleccion_borrada': $msgTexto = "¡La categoría ha sido borrada permanentemente de la base de datos!"; break;
                    }
                    if ($msgTexto != "") {
                        echo '<div class="alert alert-success alert-dismissible fade show animate__animated animate__fadeIn"><i class="bi bi-check-circle-fill me-2"></i> '.$msgTexto.'<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                    }
                }
                if (isset($_GET['error'])) {
                    echo '<div class="alert alert-danger alert-dismissible fade show animate__animated animate__shakeX"><i class="bi bi-exclamation-triangle-fill me-2"></i> Ocurrió un error al procesar la solicitud.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                }
                ?>

                <div class="row">
                    <div class="col-12 pb-5 mb-5">
                        <?php
                        // ====================================================================
                        // BLINDAJE PHP: TODO EL SWITCH ESTÁ ESTRICTAMENTE DENTRO DE ETIQUETAS
                        // ====================================================================
                        switch ($seccion) {
                            
                            // ------------------------------------------
                            // 1. SECCIÓN PEDIDOS
                            // ------------------------------------------
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
                                                <th>Detalles Pedido</th>
                                                <th>Acciones Envío</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($listaPedidos as $p) { 
                                                $datosCliente = $usu->obtenerDatosUsu($p['usuario_id']);
                                                $direccionCompleta = !empty($p['direccion_envio']) ? $p['direccion_envio'] : (($datosCliente['direccion'] ?? 'No definida') . ', ' . ($datosCliente['ciudad'] ?? '') . ' (' . ($datosCliente['codigo_postal'] ?? '') . ')');
                                            ?>
                                                <tr>
                                                    <td class="fw-bold">#<?php echo $p['id']; ?></td>
                                                    <td><?php echo htmlspecialchars($p['nombre_cliente']); ?></td>
                                                    <td><?php echo date('d/m/Y H:i', strtotime($p['fecha'])); ?></td>
                                                    <td class="fw-bold"><?php echo number_format($p['total'], 2); ?> €</td>
                                                    <td>
                                                        <form action="../controllers/adminController.php" method="POST" class="d-flex gap-2 m-0">
                                                            <input type="hidden" name="accion" value="cambiarEstadoPedido">
                                                            <input type="hidden" name="idPedido" value="<?php echo $p['id']; ?>">
                                                            <select name="nuevoEstado" class="form-select form-select-sm fw-bold" style="width: auto;">
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
                                                        <button class="btn btn-sm btn-outline-dark fw-bold" data-bs-toggle="modal" data-bs-target="#modalDetalles<?php echo $p['id']; ?>">
                                                            <i class="bi bi-card-list me-1"></i> Ver Pedido
                                                        </button>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary fw-bold" data-bs-toggle="modal" data-bs-target="#modalTracking<?php echo $p['id']; ?>">
                                                            <i class="bi bi-truck me-1"></i> Tracking
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <?php foreach ($listaPedidos as $p) { 
                                    $datosCliente = $usu->obtenerDatosUsu($p['usuario_id']);
                                    $direccionCompleta = !empty($p['direccion_envio']) ? $p['direccion_envio'] : (($datosCliente['direccion'] ?? 'No definida') . ', ' . ($datosCliente['ciudad'] ?? '') . ' (' . ($datosCliente['codigo_postal'] ?? '') . ')');
                                ?>
                                    <div class="modal fade" id="modalDetalles<?php echo $p['id']; ?>" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                            <div class="modal-content rounded-0 border-dark shadow-lg">
                                                <div class="modal-header bg-dark text-white rounded-0">
                                                    <h5 class="modal-title text-uppercase fw-bold"><i class="bi bi-box-seam me-2"></i>Preparar Pedido #<?php echo str_pad($p['id'], 5, "0", STR_PAD_LEFT); ?></h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4 bg-light">
                                                    <div class="row mb-4 bg-white p-3 border border-secondary shadow-sm">
                                                        <div class="col-md-6 mb-3 mb-md-0 border-end border-secondary">
                                                            <h6 class="fw-bold text-uppercase text-muted small mb-2"><i class="bi bi-person-fill me-1"></i>Contacto Cliente</h6>
                                                            <p class="mb-0 fw-bold fs-6"><?php echo htmlspecialchars($p['nombre_cliente']); ?></p>
                                                            <p class="mb-0 small"><a href="mailto:<?php echo htmlspecialchars($datosCliente['email']); ?>" class="text-decoration-none text-dark"><?php echo htmlspecialchars($datosCliente['email']); ?></a></p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6 class="fw-bold text-uppercase text-muted small mb-2"><i class="bi bi-geo-alt-fill me-1"></i>Dirección de Entrega</h6>
                                                            <p class="mb-0 small fw-bold" style="line-height: 1.6;"><?php echo htmlspecialchars($direccionCompleta); ?></p>
                                                        </div>
                                                    </div>

                                                    <h6 class="fw-bold text-uppercase border-bottom border-dark border-2 pb-2 mb-3 mt-4">Artículos a preparar:</h6>
                                                    <ul class="list-group list-group-flush rounded-0 shadow-sm border border-secondary">
                                                        <?php
                                                        $lineas = $pedido->obtenerInfoPedido($p['id']);
                                                        foreach ($lineas as $linea) {
                                                            $fotoMuestra = !empty($linea['url_imagen']) ? '../' . $linea['url_imagen'] : '../public/img/fondo.jpg';
                                                        ?>
                                                        <li class="list-group-item p-3 border-bottom border-secondary bg-white">
                                                            <div class="d-flex align-items-center">
                                                                <img src="<?php echo htmlspecialchars($fotoMuestra); ?>" class="me-3 border border-dark rounded-1" style="width: 80px; height: 80px; object-fit: cover;">
                                                                <div class="flex-grow-1">
                                                                    <h6 class="fw-bold text-uppercase mb-2 fs-5">
                                                                        <span class="text-primary me-1"><?php echo $linea['cantidad']; ?>x</span> <?php echo htmlspecialchars($linea['producto_nombre']); ?>
                                                                    </h6>
                                                                    <div class="d-flex flex-wrap gap-2 mb-2">
                                                                        <span class="badge bg-dark rounded-0 px-2 py-1 fs-6">Talla: <?php echo htmlspecialchars($linea['talla'] ?? 'N/A'); ?></span>
                                                                        <span class="badge border border-dark text-dark rounded-0 px-2 py-1 fs-6">Color: <?php echo htmlspecialchars($linea['color_nombre'] ?? 'N/A'); ?></span>
                                                                    </div>
                                                                    <?php if (!empty($linea['extras_texto'])): ?>
                                                                        <div class="mt-2 p-2 bg-danger bg-opacity-10 border border-danger border-opacity-50">
                                                                            <p class="mb-0 small text-danger fw-bold text-uppercase" style="letter-spacing: 0.5px;">
                                                                                <i class="bi bi-stars me-1"></i> EXTRAS: <span class="text-dark fs-6 ms-1"><?php echo htmlspecialchars($linea['extras_texto']); ?></span>
                                                                            </p>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <?php } ?>
                                                    </ul>
                                                </div>
                                                <div class="modal-footer rounded-0 bg-white">
                                                    <button type="button" class="btn btn-dark fw-bold text-uppercase px-4" data-bs-dismiss="modal">Cerrar Detalles</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="modal fade" id="modalTracking<?php echo $p['id']; ?>" tabindex="-1">
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
                                                            <label class="form-label small fw-bold text-muted text-uppercase">Destinatario:</label>
                                                            <input type="text" class="form-control bg-light border-dark" value="<?php echo htmlspecialchars($p['nombre_cliente'] . ' ('.$datosCliente['email'].')'); ?>" readonly>
                                                            <input type="hidden" name="email_cliente" value="<?php echo $datosCliente['email']; ?>">
                                                            <input type="hidden" name="nombre_cliente" value="<?php echo $p['nombre_cliente']; ?>">
                                                        </div>
                                                        <hr class="border-secondary">
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-bold text-uppercase">Número de Seguimiento (Tracking):</label>
                                                            <input type="text" name="tracking_number" class="form-control border-dark" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-bold text-uppercase">Enlace web (URL):</label>
                                                            <input type="url" name="tracking_url" class="form-control border-dark" placeholder="https://www.correos.es/tracking" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer bg-light border-top rounded-0">
                                                        <button type="submit" class="btn btn-dark rounded-0 btn-sm fw-bold"><i class="bi bi-send me-1"></i> Disparar Correo</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                        <?php
                                break;

                            // -----------------------------------------------------------------
                            // 2. SECCIÓN PRODUCTOS: FILTRO DE CATEGORÍAS Y PAGINACIÓN AVANZADA
                            // -----------------------------------------------------------------
                            case 'productos':
                                
                                $filtroColeccion = isset($_GET['filtro_coleccion']) ? (int)$_GET['filtro_coleccion'] : 0;
                                $productosPorPagina = 10; 
                                $paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
                                if ($paginaActual < 1) $paginaActual = 1;

                                // VERIFICAR SI EXISTE LA TABLA PIVOTE (Para retrocompatibilidad)
                                $hasPivot = false;
                                try {
                                    $conexion->query("SELECT 1 FROM producto_colecciones LIMIT 1");
                                    $hasPivot = true;
                                } catch(PDOException $e) {}

                                // 1. CONTAR TOTAL DE PRODUCTOS (Respetando el filtro si lo hay)
                                $sqlCount = "SELECT COUNT(DISTINCT p.id) FROM productos p";
                                $paramsCount = [];
                                if ($filtroColeccion > 0) {
                                    if ($hasPivot) {
                                        $sqlCount .= " INNER JOIN producto_colecciones pc ON p.id = pc.producto_id WHERE p.es_segunda_mano = 0 AND pc.coleccion_id = ?";
                                    } else {
                                        $sqlCount .= " WHERE p.es_segunda_mano = 0 AND p.coleccion_id = ?";
                                    }
                                    $paramsCount[] = $filtroColeccion;
                                } else {
                                    $sqlCount .= " WHERE p.es_segunda_mano = 0";
                                }

                                $stmtC = $conexion->prepare($sqlCount);
                                $stmtC->execute($paramsCount);
                                $totalProductos = $stmtC->fetchColumn();

                                $totalPaginas = ceil($totalProductos / $productosPorPagina);
                                $offset = ($paginaActual - 1) * $productosPorPagina;

                                // 2. OBTENER IDs DE ESTA PÁGINA ESPECÍFICA
                                $sqlIds = "SELECT DISTINCT p.id FROM productos p";
                                if ($filtroColeccion > 0) {
                                    if ($hasPivot) {
                                        $sqlIds .= " INNER JOIN producto_colecciones pc ON p.id = pc.producto_id WHERE p.es_segunda_mano = 0 AND pc.coleccion_id = ?";
                                    } else {
                                        $sqlIds .= " WHERE p.es_segunda_mano = 0 AND p.coleccion_id = ?";
                                    }
                                } else {
                                    $sqlIds .= " WHERE p.es_segunda_mano = 0";
                                }
                                $sqlIds .= " ORDER BY p.id DESC LIMIT $productosPorPagina OFFSET $offset";
                                
                                $stmtIds = $conexion->prepare($sqlIds);
                                $stmtIds->execute($paramsCount);
                                $idsPagina = $stmtIds->fetchAll(PDO::FETCH_COLUMN);

                                // 3. CARGAR VARIANTES Y COLECCIONES
                                $listaInventario = [];
                                $prodCols = [];

                                if (!empty($idsPagina)) {
                                    $inQuery = implode(',', $idsPagina);
                                    
                                    // Datos de productos
                                    $sqlInv = "SELECT p.id as prenda_id, p.nombre, p.precio, p.rebaja, p.activo, p.destacado, p.coleccion_id, p.es_segunda_mano,
                                                      c.id as color_id, c.nombre as nombre_color
                                               FROM productos p
                                               LEFT JOIN producto_colores pc ON p.id = pc.producto_id
                                               LEFT JOIN colores c ON pc.color_id = c.id
                                               WHERE p.id IN ($inQuery)
                                               ORDER BY p.id DESC";
                                    $stmtInv = $conexion->query($sqlInv);
                                    $listaInventario = $stmtInv->fetchAll(PDO::FETCH_ASSOC);

                                    // Si existe el pivote, cargamos todas las categorías de esos productos
                                    if ($hasPivot) {
                                        $sqlCols = "SELECT producto_id, coleccion_id FROM producto_colecciones WHERE producto_id IN ($inQuery)";
                                        $stmtCols = $conexion->query($sqlCols);
                                        $prodColsRaw = $stmtCols->fetchAll(PDO::FETCH_ASSOC);
                                        foreach($prodColsRaw as $pc) {
                                            $prodCols[$pc['producto_id']][] = $pc['coleccion_id'];
                                        }
                                    }
                                }

                                // 4. AGRUPAR VARIANTE Y COLECCIONES
                                $productosAgrupados = [];
                                if (!empty($listaInventario)) {
                                    foreach ($listaInventario as $item) {
                                        $pId = $item['prenda_id'];
                                        if (!isset($productosAgrupados[$pId])) {
                                            
                                            // Asignamos las colecciones (las múltiples si existe el pivote, o la individual si no)
                                            $coleccionesDelProducto = [];
                                            if ($hasPivot && isset($prodCols[$pId]) && !empty($prodCols[$pId])) {
                                                $coleccionesDelProducto = $prodCols[$pId];
                                            } else if (!empty($item['coleccion_id'])) {
                                                $coleccionesDelProducto = [$item['coleccion_id']];
                                            }

                                            $productosAgrupados[$pId] = [
                                                'producto_id' => $item['prenda_id'],
                                                'nombre' => $item['nombre'],
                                                'precio' => $item['precio'],
                                                'rebaja' => $item['rebaja'],
                                                'activo' => $item['activo'],
                                                'destacado' => $item['destacado'], 
                                                'colecciones' => $coleccionesDelProducto,
                                                'es_segunda_mano' => $item['es_segunda_mano'],
                                                'variantes' => []
                                            ];
                                        }
                                        if (!empty($item['color_id'])) {
                                            $productosAgrupados[$pId]['variantes'][$item['color_id']] = [
                                                'color_id' => $item['color_id'],
                                                'equipacion' => $item['nombre_color']
                                            ];
                                        }
                                    }
                                }

                                // PARTIMOS LOS PRODUCTOS EN DOS COLUMNAS
                                $columnaIzq = array_slice($productosAgrupados, 0, 5, true);
                                $columnaDer = array_slice($productosAgrupados, 5, 5, true);
                        ?>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h3 class="fw-bold m-0 text-uppercase">Gestión de Inventario</h3>
                                    <button class="btn btn-admin-black px-3 py-2" type="button" data-bs-toggle="collapse" data-bs-target="#formNuevaPrenda">
                                        <i class="bi bi-plus-lg me-2"></i> Crear Producto Nuevo
                                    </button>
                                </div>

                                <!-- BARRA DE FILTRO POR CATEGORÍAS -->
                                <div class="card p-3 mb-4 bg-light border-dark shadow-sm">
                                    <form method="GET" action="admin.php" class="row g-2 align-items-center">
                                        <input type="hidden" name="seccion" value="productos">
                                        <div class="col-auto">
                                            <label class="fw-bold text-uppercase small"><i class="bi bi-funnel-fill me-1"></i> Filtrar por Liga/Categoría:</label>
                                        </div>
                                        <div class="col-md-5 col-12">
                                            <select name="filtro_coleccion" class="form-select border-dark shadow-sm fw-bold">
                                                <option value="0">--- VER TODO EL CATÁLOGO ---</option>
                                                <?php foreach ($listaColeciones as $c) { 
                                                    $sel = ($filtroColeccion == $c['id']) ? 'selected' : '';
                                                    echo "<option value='{$c['id']}' $sel>{$c['nombre']}</option>";
                                                } ?>
                                            </select>
                                        </div>
                                        <div class="col-auto">
                                            <button type="submit" class="btn btn-dark fw-bold">Aplicar Filtro</button>
                                        </div>
                                        <?php if($filtroColeccion > 0): ?>
                                            <div class="col-auto">
                                                <a href="admin.php?seccion=productos" class="btn btn-outline-danger fw-bold"><i class="bi bi-x-circle me-1"></i> Quitar filtro</a>
                                            </div>
                                        <?php endif; ?>
                                    </form>
                                </div>

                                <?php if ($totalPaginas > 1): ?>
                                    <div class="d-flex justify-content-center mb-4 pb-3 border-bottom">
                                        <nav aria-label="Paginación de inventario superior">
                                            <ul class="pagination mb-0 shadow-sm">
                                                <?php $disabledPrev = ($paginaActual <= 1) ? 'disabled' : ''; ?>
                                                <li class="page-item <?php echo $disabledPrev; ?>"><a class="page-link text-dark" href="admin.php?seccion=productos&filtro_coleccion=<?= $filtroColeccion ?>&pagina=<?= $paginaActual - 1 ?>">Anterior</a></li>
                                                
                                                <?php 
                                                $rango = 2; 
                                                $inicio = max(1, $paginaActual - $rango);
                                                $fin = min($totalPaginas, $paginaActual + $rango);

                                                if ($inicio > 1) {
                                                    echo '<li class="page-item"><a class="page-link text-dark" href="admin.php?seccion=productos&filtro_coleccion='.$filtroColeccion.'&pagina=1">1</a></li>';
                                                    if ($inicio > 2) {
                                                        echo '<li class="page-item disabled"><span class="page-link text-dark border-0">...</span></li>';
                                                    }
                                                }

                                                for ($i = $inicio; $i <= $fin; $i++) { 
                                                    $activa = ($i == $paginaActual) ? 'active bg-dark border-dark text-white' : 'text-dark'; 
                                                    echo '<li class="page-item"><a class="page-link '.$activa.'" href="admin.php?seccion=productos&filtro_coleccion='.$filtroColeccion.'&pagina='.$i.'">'.$i.'</a></li>';
                                                } 

                                                if ($fin < $totalPaginas) {
                                                    if ($fin < $totalPaginas - 1) {
                                                        echo '<li class="page-item disabled"><span class="page-link text-dark border-0">...</span></li>';
                                                    }
                                                    echo '<li class="page-item"><a class="page-link text-dark" href="admin.php?seccion=productos&filtro_coleccion='.$filtroColeccion.'&pagina='.$totalPaginas.'">'.$totalPaginas.'</a></li>';
                                                }
                                                ?>

                                                <?php $disabledNext = ($paginaActual >= $totalPaginas) ? 'disabled' : ''; ?>
                                                <li class="page-item <?php echo $disabledNext; ?>"><a class="page-link text-dark" href="admin.php?seccion=productos&filtro_coleccion=<?= $filtroColeccion ?>&pagina=<?= $paginaActual + 1 ?>">Siguiente</a></li>
                                            </ul>
                                        </nav>
                                    </div>
                                <?php endif; ?>

                                <div class="collapse mb-4 mt-3" id="formNuevaPrenda">
                                    <div class="card card-body admin-card border-0 shadow-sm bg-light">
                                        <h5 class="fw-bold mb-3 text-uppercase"><i class="bi bi-box-seam me-2"></i>Añadir Nueva Camiseta al Catálogo</h5>
                                        <form action="../controllers/adminController.php" method="POST" enctype="multipart/form-data" class="row g-3">
                                            <input type="hidden" name="accion" value="crearPrendaTienda">

                                            <div class="col-md-3">
                                                <label class="fw-bold small">Nombre del Producto:</label>
                                                <input type="text" name="nombre" class="form-control border-dark" placeholder="Ej: Real Madrid 24/25" required>
                                            </div>
                                            <div class="col-6 col-md-2">
                                                <label class="fw-bold small">Precio Base (€):</label>
                                                <input type="number" step="0.01" name="precio" class="form-control border-dark" value="17.00" required>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <label class="fw-bold small">Equipación Principal:</label>
                                                <select name="equipacion" class="form-select border-dark" required>
                                                    <option value="Local">Local</option>
                                                    <option value="Visitante">Visitante</option>
                                                    <option value="Tercera">Tercera Equipación</option>
                                                </select>
                                            </div>
                                            <!-- AÑADIDO SELECT MÚLTIPLE PARA CATEGORÍAS -->
                                            <div class="col-12 col-md-4">
                                                <label class="fw-bold small">Categoría/s (Ligas):</label>
                                                <select name="coleccion_id[]" class="form-select border-dark" multiple size="3" required>
                                                    <?php foreach ($listaColeciones as $c) { ?>
                                                        <option value="<?php echo $c['id']; ?>"><?php echo $c['nombre']; ?></option>
                                                    <?php } ?>
                                                </select>
                                                <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">(Ctrl/Cmd + Clic para seleccionar varias)</small>
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

                                <form action="../controllers/adminController.php" method="POST" id="formMasivo">
                                    <input type="hidden" name="accion" value="actualizarInventarioMasivo">
                                    <input type="hidden" name="pagina_retorno" value="<?php echo $paginaActual; ?>">
                                    <input type="hidden" name="filtro_coleccion_retorno" value="<?php echo $filtroColeccion; ?>">

                                    <?php if (empty($columnaIzq) && empty($columnaDer)) { ?>
                                        <div class="alert alert-secondary text-center py-5">
                                            <i class="bi bi-search display-4 d-block mb-3"></i>
                                            <h4 class="fw-bold text-uppercase">Sin resultados</h4>
                                            <p class="mb-0">No se han encontrado productos en esta categoría o página.</p>
                                        </div>
                                    <?php } else { ?>
                                        
                                        <div class="row g-4">
                                            
                                            <div class="col-12 col-lg-6">
                                                <div class="d-flex flex-column gap-4">
                                                    <?php foreach ($columnaIzq as $id => $datos) { 
                                                        $stmtDesc = $conexion->prepare("SELECT descripcion FROM productos WHERE id = ?");
                                                        $stmtDesc->execute([$id]);
                                                        $descReal = $stmtDesc->fetchColumn();
                                                    ?>
                                                        <div class="card border-0 shadow-sm admin-card" style="border-left: 6px solid #0dcaf0;">
                                                            <div class="card-header bg-dark text-white py-3">
                                                                <div class="row align-items-center g-2">
                                                                    <div class="col-12 col-sm-5">
                                                                        <div class="d-flex align-items-center gap-1">
                                                                            <span class="text-secondary fw-bold small">#<?php echo $id; ?></span>
                                                                            <input type="text" name="nombre[<?php echo $id; ?>]" value="<?php echo htmlspecialchars($datos['nombre']); ?>" class="form-control form-control-sm border-0 bg-secondary text-dark fw-bold text-uppercase w-100" style="letter-spacing: 0.5px; background-color: #f8f9fa;" required>
                                                                        </div>
                                                                    </div>
                                                                    <!-- AÑADIDO SELECT MÚLTIPLE DE CATEGORÍAS -->
                                                                    <div class="col-4 col-sm-3">
                                                                        <select name="coleccion[<?php echo $id; ?>][]" class="form-select form-select-sm border-dark bg-light text-dark fw-bold w-100" style="font-size: 0.75rem; min-height: 70px;" multiple required>
                                                                            <?php foreach ($listaColeciones as $col) { 
                                                                                $seleccionado = (in_array($col['id'], $datos['colecciones'])) ? 'selected' : '';
                                                                            ?>
                                                                                <option value="<?php echo $col['id']; ?>" <?php echo $seleccionado; ?>><?php echo htmlspecialchars($col['nombre']); ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-4 col-sm-2">
                                                                        <div class="input-group input-group-sm">
                                                                            <input type="number" step="0.01" name="precio[<?php echo $id; ?>]" value="<?php echo $datos['precio']; ?>" class="form-control text-center fw-bold border-0 bg-light text-dark p-1" style="font-size: 0.8rem;">
                                                                            <span class="input-group-text bg-light border-0 fw-bold text-dark p-1">€</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-4 col-sm-2">
                                                                        <div class="input-group input-group-sm">
                                                                            <input type="number" name="rebaja[<?php echo $id; ?>]" value="<?php echo $datos['rebaja']; ?>" class="form-control text-center fw-bold p-1" style="font-size: 0.8rem;" min="0" max="100">
                                                                            <span class="input-group-text bg-secondary text-white border-0 small p-1">%</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12 mt-2">
                                                                        <div class="d-flex align-items-center justify-content-between">
                                                                            <div class="input-group input-group-sm me-2">
                                                                                <span class="input-group-text bg-secondary text-white border-0 small font-monospace">INFO</span>
                                                                                <input type="text" name="descripcion[<?php echo $id; ?>]" value="<?php echo htmlspecialchars($descReal ?? ''); ?>" class="form-control bg-dark text-white border-0 small" placeholder="Descripción breve">
                                                                            </div>
                                                                            <select name="destacado[<?php echo $id; ?>]" class="form-select form-select-sm fw-bold border-0 w-auto me-2 <?php echo ($datos['destacado'] == 1 ? 'text-warning bg-dark' : 'text-muted'); ?>">
                                                                                <option value="1" <?php echo ($datos['destacado'] == 1 ? 'selected' : ''); ?>>★ DEST</option>
                                                                                <option value="0" <?php echo ($datos['destacado'] == 0 ? 'selected' : ''); ?>>NORMAL</option>
                                                                            </select>
                                                                            <select name="activo[<?php echo $id; ?>]" class="form-select form-select-sm fw-bold border-0 w-auto <?php echo ($datos['activo'] == 1 ? 'text-success' : 'text-danger'); ?>">
                                                                                <option value="1" <?php echo ($datos['activo'] == 1 ? 'selected' : ''); ?>>ACT</option>
                                                                                <option value="0" <?php echo ($datos['activo'] == 0 ? 'selected' : ''); ?>>OCU</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="card-body bg-white border border-top-0 border-light p-3">
                                                                <ul class="nav nav-tabs nav-tabs-scroll" role="tablist">
                                                                    <?php 
                                                                    $vIndex = 0;
                                                                    foreach($datos['variantes'] as $color_id => $var): 
                                                                        $isActive = ($vIndex == 0) ? 'active' : '';
                                                                    ?>
                                                                    <li class="nav-item flex-shrink-0" role="presentation">
                                                                        <button class="nav-link text-dark text-uppercase <?= $isActive ?>" data-bs-toggle="tab" data-bs-target="#variante-<?= $id ?>-<?= $color_id ?>" type="button" role="tab">
                                                                            <i class="bi bi-tag-fill me-1"></i> <?= htmlspecialchars($var['equipacion']) ?>
                                                                        </button>
                                                                    </li>
                                                                    <?php $vIndex++; endforeach; ?>
                                                                    
                                                                    <li class="nav-item ms-auto flex-shrink-0">
                                                                        <button type="button" class="btn btn-sm btn-warning fw-bold text-dark mt-1 mx-1" data-bs-toggle="modal" data-bs-target="#modalVariante<?= $id ?>">
                                                                            <i class="bi bi-plus-circle-fill"></i> Variante
                                                                        </button>
                                                                    </li>
                                                                </ul>

                                                                <div class="tab-content mt-3">
                                                                    <?php 
                                                                    $vIndex = 0;
                                                                    foreach($datos['variantes'] as $color_id => $var): 
                                                                        $isActive = ($vIndex == 0) ? 'show active' : '';
                                                                        
                                                                        $stmtFotos = $conexion->prepare("SELECT id, url_imagen FROM imagenes_productos WHERE producto_id = ? AND color_id = ?");
                                                                        $stmtFotos->execute([$id, $color_id]);
                                                                        $fotosProducto = $stmtFotos->fetchAll(PDO::FETCH_ASSOC);
                                                                    ?>
                                                                    <div class="tab-pane fade <?= $isActive ?>" id="variante-<?= $id ?>-<?= $color_id ?>" role="tabpanel">
                                                                        <div class="d-flex justify-content-end mb-2">
                                                                            <a href="../controllers/adminController.php?accion=borrarVariante&p_id=<?= $id; ?>&c_id=<?= $color_id; ?>&pag=<?= $paginaActual; ?>&filtro=<?= $filtroColeccion; ?>" class="btn btn-sm btn-outline-danger py-1 px-2 fw-bold" style="font-size: 0.75rem;" onclick="return confirm('¿Estás seguro de que quieres eliminar TODA esta variante? Se borrarán todas sus fotos, stock y tallas de la base de datos.');">
                                                                                <i class="bi bi-trash-fill"></i> Borrar Equipación
                                                                            </a>
                                                                        </div>
                                                                        <div class="d-flex flex-wrap align-items-center bg-light p-2 rounded border border-light gap-2">
                                                                            <?php foreach ($fotosProducto as $ft) { ?>
                                                                                <div class="crm-thumb-container m-0">
                                                                                    <img src="../<?= htmlspecialchars($ft['url_imagen']); ?>" class="crm-thumb shadow-sm">
                                                                                    <a href="../controllers/adminController.php?accion=borrarFotoEspecifica&id_foto=<?= $ft['id']; ?>&p_id=<?= $id; ?>&pag=<?= $paginaActual; ?>&filtro=<?= $filtroColeccion; ?>" class="btn-borrar-foto" onclick="return confirm('¿Borrar foto?');">×</a>
                                                                                </div>
                                                                            <?php } ?>
                                                                            <button type="button" class="btn btn-outline-secondary bg-white shadow-sm d-flex align-items-center justify-content-center p-0" style="height: 55px; width: 55px; border-style: dashed; border-width: 2px;" onclick="document.getElementById('add-foto-input-<?= $id ?>-<?= $color_id ?>').click();" title="Añadir foto">
                                                                                <i class="bi bi-plus-lg fs-5 text-dark"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                    <?php $vIndex++; endforeach; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>

                                            <div class="col-12 col-lg-6">
                                                <div class="d-flex flex-column gap-4">
                                                    <?php foreach ($columnaDer as $id => $datos) { 
                                                        $stmtDesc = $conexion->prepare("SELECT descripcion FROM productos WHERE id = ?");
                                                        $stmtDesc->execute([$id]);
                                                        $descReal = $stmtDesc->fetchColumn();
                                                    ?>
                                                        <div class="card border-0 shadow-sm admin-card" style="border-left: 6px solid #0dcaf0;">
                                                            <div class="card-header bg-dark text-white py-3">
                                                                <div class="row align-items-center g-2">
                                                                    <div class="col-12 col-sm-5">
                                                                        <div class="d-flex align-items-center gap-1">
                                                                            <span class="text-secondary fw-bold small">#<?php echo $id; ?></span>
                                                                            <input type="text" name="nombre[<?php echo $id; ?>]" value="<?php echo htmlspecialchars($datos['nombre']); ?>" class="form-control form-control-sm border-0 bg-secondary text-dark fw-bold text-uppercase w-100" style="letter-spacing: 0.5px; background-color: #f8f9fa;" required>
                                                                        </div>
                                                                    </div>
                                                                    <!-- AÑADIDO SELECT MÚLTIPLE DE CATEGORÍAS -->
                                                                    <div class="col-4 col-sm-3">
                                                                        <select name="coleccion[<?php echo $id; ?>][]" class="form-select form-select-sm border-dark bg-light text-dark fw-bold w-100" style="font-size: 0.75rem; min-height: 70px;" multiple required>
                                                                            <?php foreach ($listaColeciones as $col) { 
                                                                                $seleccionado = (in_array($col['id'], $datos['colecciones'])) ? 'selected' : '';
                                                                            ?>
                                                                                <option value="<?php echo $col['id']; ?>" <?php echo $seleccionado; ?>><?php echo htmlspecialchars($col['nombre']); ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                </div>
                                                                <div class="col-4 col-sm-2">
                                                                    <div class="input-group input-group-sm">
                                                                        <input type="number" step="0.01" name="precio[<?php echo $id; ?>]" value="<?php echo $datos['precio']; ?>" class="form-control text-center fw-bold border-0 bg-light text-dark p-1" style="font-size: 0.8rem;">
                                                                        <span class="input-group-text bg-light border-0 fw-bold text-dark p-1">€</span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-4 col-sm-2">
                                                                    <div class="input-group input-group-sm">
                                                                        <input type="number" name="rebaja[<?php echo $id; ?>]" value="<?php echo $datos['rebaja']; ?>" class="form-control text-center fw-bold p-1" style="font-size: 0.8rem;" min="0" max="100">
                                                                        <span class="input-group-text bg-secondary text-white border-0 small p-1">%</span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 mt-2">
                                                                    <div class="d-flex align-items-center justify-content-between">
                                                                        <div class="input-group input-group-sm me-2">
                                                                            <span class="input-group-text bg-secondary text-white border-0 small font-monospace">INFO</span>
                                                                            <input type="text" name="descripcion[<?php echo $id; ?>]" value="<?php echo htmlspecialchars($descReal ?? ''); ?>" class="form-control bg-dark text-white border-0 small" placeholder="Descripción breve">
                                                                        </div>
                                                                        <select name="destacado[<?php echo $id; ?>]" class="form-select form-select-sm fw-bold border-0 w-auto me-2 <?php echo ($datos['destacado'] == 1 ? 'text-warning bg-dark' : 'text-muted'); ?>">
                                                                            <option value="1" <?php echo ($datos['destacado'] == 1 ? 'selected' : ''); ?>>★ DEST</option>
                                                                            <option value="0" <?php echo ($datos['destacado'] == 0 ? 'selected' : ''); ?>>NORMAL</option>
                                                                        </select>
                                                                        <select name="activo[<?php echo $id; ?>]" class="form-select form-select-sm fw-bold border-0 w-auto <?php echo ($datos['activo'] == 1 ? 'text-success' : 'text-danger'); ?>">
                                                                            <option value="1" <?php echo ($datos['activo'] == 1 ? 'selected' : ''); ?>>ACT</option>
                                                                            <option value="0" <?php echo ($datos['activo'] == 0 ? 'selected' : ''); ?>>OCU</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="card-body bg-white border border-top-0 border-light p-3">
                                                            <ul class="nav nav-tabs nav-tabs-scroll" role="tablist">
                                                                <?php 
                                                                $vIndex = 0;
                                                                foreach($datos['variantes'] as $color_id => $var): 
                                                                    $isActive = ($vIndex == 0) ? 'active' : '';
                                                                ?>
                                                                <li class="nav-item flex-shrink-0" role="presentation">
                                                                    <button class="nav-link text-dark text-uppercase <?= $isActive ?>" data-bs-toggle="tab" data-bs-target="#variante-<?= $id ?>-<?= $color_id ?>" type="button" role="tab">
                                                                        <i class="bi bi-tag-fill me-1"></i> <?= htmlspecialchars($var['equipacion']) ?>
                                                                    </button>
                                                                </li>
                                                                <?php $vIndex++; endforeach; ?>
                                                                
                                                                <li class="nav-item ms-auto flex-shrink-0">
                                                                    <button type="button" class="btn btn-sm btn-warning fw-bold text-dark mt-1 mx-1" data-bs-toggle="modal" data-bs-target="#modalVariante<?= $id ?>">
                                                                        <i class="bi bi-plus-circle-fill"></i> Variante
                                                                    </button>
                                                                </li>
                                                            </ul>

                                                            <div class="tab-content mt-3">
                                                                <?php 
                                                                $vIndex = 0;
                                                                foreach($datos['variantes'] as $color_id => $var): 
                                                                    $isActive = ($vIndex == 0) ? 'show active' : '';
                                                                    
                                                                    $stmtFotos = $conexion->prepare("SELECT id, url_imagen FROM imagenes_productos WHERE producto_id = ? AND color_id = ?");
                                                                    $stmtFotos->execute([$id, $color_id]);
                                                                    $fotosProducto = $stmtFotos->fetchAll(PDO::FETCH_ASSOC);
                                                                ?>
                                                                <div class="tab-pane fade <?= $isActive ?>" id="variante-<?= $id ?>-<?= $color_id ?>" role="tabpanel">
                                                                    <div class="d-flex justify-content-end mb-2">
                                                                        <a href="../controllers/adminController.php?accion=borrarVariante&p_id=<?= $id; ?>&c_id=<?= $color_id; ?>&pag=<?= $paginaActual; ?>&filtro=<?= $filtroColeccion; ?>" class="btn btn-sm btn-outline-danger py-1 px-2 fw-bold" style="font-size: 0.75rem;" onclick="return confirm('¿Estás seguro de que quieres eliminar TODA esta variante? Se borrarán todas sus fotos, stock y tallas de la base de datos.');">
                                                                            <i class="bi bi-trash-fill"></i> Borrar Equipación
                                                                        </a>
                                                                    </div>
                                                                    <div class="d-flex flex-wrap align-items-center bg-light p-2 rounded border border-light gap-2">
                                                                        <?php foreach ($fotosProducto as $ft) { ?>
                                                                            <div class="crm-thumb-container m-0">
                                                                                <img src="../<?= htmlspecialchars($ft['url_imagen']); ?>" class="crm-thumb shadow-sm">
                                                                                <a href="../controllers/adminController.php?accion=borrarFotoEspecifica&id_foto=<?= $ft['id']; ?>&p_id=<?= $id; ?>&pag=<?= $paginaActual; ?>&filtro=<?= $filtroColeccion; ?>" class="btn-borrar-foto" onclick="return confirm('¿Borrar foto?');">×</a>
                                                                            </div>
                                                                        <?php } ?>
                                                                        <button type="button" class="btn btn-outline-secondary bg-white shadow-sm d-flex align-items-center justify-content-center p-0" style="height: 55px; width: 55px; border-style: dashed; border-width: 2px;" onclick="document.getElementById('add-foto-input-<?= $id ?>-<?= $color_id ?>').click();" title="Añadir foto">
                                                                            <i class="bi bi-plus-lg fs-5 text-dark"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                <?php $vIndex++; endforeach; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                                </div>
                                            </div>

                                        </div>

                                    <?php } ?>

                                    <div class="btn-flotante-guardar d-none d-md-block">
                                        <button type="submit" form="formMasivo" class="btn btn-dark btn-lg shadow-lg fw-bold px-5 py-3 rounded-pill border border-2 border-light text-uppercase ls-1">
                                            <i class="bi bi-save-fill fs-5 me-2"></i> Guardar Cambios
                                        </button>
                                    </div>
                                    <div class="btn-flotante-movil d-block d-md-none">
                                        <button type="submit" form="formMasivo" class="btn btn-dark btn-lg shadow fw-bold w-100 text-uppercase">
                                            <i class="bi bi-save-fill me-2"></i> Guardar Cambios
                                        </button>
                                    </div>
                                </form>

                                <?php foreach ($productosAgrupados as $id => $datos) { ?>
                                    <div class="modal fade" id="modalVariante<?php echo $id; ?>" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-dark rounded-0 shadow-lg">
                                                <div class="modal-header bg-warning text-dark rounded-0">
                                                    <h5 class="modal-title fw-bold text-uppercase"><i class="bi bi-plus-circle-fill me-2"></i>Añadir Variante</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="../controllers/adminController.php" method="POST" enctype="multipart/form-data">
                                                    <div class="modal-body p-4">
                                                        <input type="hidden" name="accion" value="anadirEquipacionExtra">
                                                        <input type="hidden" name="producto_id" value="<?php echo $id; ?>">
                                                        <input type="hidden" name="pagina_retorno" value="<?php echo $paginaActual; ?>">
                                                        <input type="hidden" name="filtro_coleccion_retorno" value="<?php echo $filtroColeccion; ?>">
                                                        
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small text-uppercase">Modelo Base:</label>
                                                            <input type="text" class="form-control border-dark bg-light text-muted fw-bold" value="<?php echo htmlspecialchars($datos['nombre']); ?>" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small text-uppercase">Elige la nueva equipación:</label>
                                                            <select name="equipacion" class="form-select border-dark shadow-sm py-2" required>
                                                                <option value="Local">Local</option>
                                                                <option value="Visitante">Visitante</option>
                                                                <option value="Tercera Equipación">Tercera Equipación</option>
                                                                <option value="Cuarta Equipación">Cuarta Equipación</option>
                                                                <option value="Portero">Portero</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small text-uppercase">Sube las fotos de esta equipación:</label>
                                                            <input type="file" name="imagenes[]" class="form-control border-dark py-2 shadow-sm" accept="image/*" multiple required>
                                                            <small class="text-muted d-block mt-2"><i class="bi bi-info-circle me-1"></i>Selecciona todas las fotos juntas. La primera será la portada de la equipación.</small>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer rounded-0 bg-light">
                                                        <button type="submit" class="btn btn-warning fw-bold px-4 w-100 border-dark text-dark text-uppercase"><i class="bi bi-cloud-arrow-up-fill me-2"></i>Guardar Variante</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <?php foreach($datos['variantes'] as $color_id => $var) { ?>
                                        <form action="../controllers/adminController.php" method="POST" enctype="multipart/form-data" class="d-none" id="form-add-foto-<?= $id ?>-<?= $color_id ?>">
                                            <input type="hidden" name="accion" value="anadirFotosGaleriaExistente">
                                            <input type="hidden" name="producto_id" value="<?= $id ?>">
                                            <input type="hidden" name="color_id" value="<?= $color_id ?>">
                                            <input type="hidden" name="pagina_retorno" value="<?= $paginaActual ?>">
                                            <input type="hidden" name="filtro_coleccion_retorno" value="<?= $filtroColeccion; ?>">
                                            <input type="file" id="add-foto-input-<?= $id ?>-<?= $color_id ?>" name="imagenes[]" onchange="document.getElementById('form-add-foto-<?= $id ?>-<?= $color_id ?>').submit();" multiple>
                                        </form>
                                    <?php } ?>
                                <?php } ?>

                        <?php break; ?>
                        
                            <?php
                            // ------------------------------------------
                            // 3. SECCIÓN COLECCIONES / CATEGORÍAS
                            // ------------------------------------------
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
                                                <th>Precio Masivo</th>
                                                <th>Rebaja Masiva</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($todasLasColecciones as $col) { 
                                                // Consultar el descuento actual de la liga como referencia
                                                $stmtReb = $conexion->prepare("SELECT rebaja FROM productos WHERE coleccion_id = ? AND rebaja > 0 LIMIT 1");
                                                $stmtReb->execute([$col['id']]);
                                                $rebajaRealCol = $stmtReb->fetchColumn();
                                                $mostrarRebaja = $rebajaRealCol ? (int)$rebajaRealCol : 0;

                                                // Consultar el precio actual del primer producto de la liga como referencia
                                                $stmtPrecio = $conexion->prepare("SELECT precio FROM productos WHERE coleccion_id = ? LIMIT 1");
                                                $stmtPrecio->execute([$col['id']]);
                                                $precioRefCol = $stmtPrecio->fetchColumn();
                                                $mostrarPrecio = $precioRefCol ? number_format((float)$precioRefCol, 2, '.', '') : '';
                                            ?>
                                                <tr>
                                                    <td class="text-center text-secondary fw-bold">#<?php echo $col['id']; ?></td>
                                                    <td>
                                                        <input type="text" name="nombre" value="<?php echo htmlspecialchars($col['nombre']); ?>" class="form-control form-control-sm fw-bold border-dark" form="form_col_<?php echo $col['id']; ?>">
                                                    </td>
                                                    <td>
                                                        <textarea name="descripcion" class="form-control form-control-sm border-dark" rows="1" form="form_col_<?php echo $col['id']; ?>"><?php echo htmlspecialchars($col['descripcion'] ?? ''); ?></textarea>
                                                    </td>
                                                    <td>
                                                        <div class="input-group input-group-sm">
                                                            <input type="number" step="0.01" name="precio_masivo" class="form-control border-dark text-center fw-bold text-primary" value="<?php echo $mostrarPrecio; ?>" min="0" placeholder="Ej: 19.99" form="form_col_<?php echo $col['id']; ?>">
                                                            <span class="input-group-text bg-dark text-white border-dark">€</span>
                                                        </div>
                                                        <small class="text-muted d-block text-center mt-1" style="font-size: 0.65rem;">Se aplica a toda la liga</small>
                                                    </td>
                                                    <td>
                                                        <div class="input-group input-group-sm">
                                                            <input type="number" name="descuento_masivo" class="form-control border-dark text-center fw-bold text-danger" value="<?php echo $mostrarRebaja; ?>" min="0" max="100" form="form_col_<?php echo $col['id']; ?>">
                                                            <span class="input-group-text bg-dark text-white border-dark">%</span>
                                                        </div>
                                                        <small class="text-muted d-block text-center mt-1" style="font-size: 0.65rem;">Se aplica a toda la liga</small>
                                                    </td>
                                                    <td>
                                                        <select name="nuevo_estado" class="form-select form-select-sm border-dark" form="form_col_<?php echo $col['id']; ?>">
                                                            <option value="1" <?php echo ($col['activa'] == 1 ? 'selected' : ''); ?>>Activa</option>
                                                            <option value="2" <?php echo ($col['activa'] == 2 ? 'selected' : ''); ?>>Inactiva</option>
                                                        </select>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center gap-1">
                                                            <form id="form_col_<?php echo $col['id']; ?>" action="../controllers/adminController.php" method="POST" class="m-0">
                                                                <input type="hidden" name="accion" value="actualizarColeccion">
                                                                <input type="hidden" name="id_coleccion" value="<?php echo $col['id']; ?>">
                                                                <button type="submit" class="btn btn-sm btn-dark px-2" title="Guardar cambios"><i class="bi bi-check-lg fs-6"></i></button>
                                                            </form>

                                                            <a href="../controllers/adminController.php?accion=borrarColeccion&id=<?php echo $col['id']; ?>" 
                                                               class="btn btn-sm btn-danger px-2 d-flex align-items-center" 
                                                               onclick="return confirm('¡ATENCIÓN! ¿Estás totalmente seguro de que quieres BORRAR la categoría \&quot;<?php echo addslashes($col['nombre']); ?>\&quot; de la base de datos?');" 
                                                               title="Borrar categoría">
                                                                <i class="bi bi-trash-fill fs-6"></i>
                                                            </a>
                                                        </div>
                                                    </td>
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
            
            window.addEventListener('paste', (e) => {
                let formPrenda = document.getElementById('formNuevaPrenda');
                if (formPrenda && formPrenda.classList.contains('show')) {
                    if (e.clipboardData && e.clipboardData.files.length > 0) {
                        if(e.clipboardData.files[0].type.startsWith('image/')) {
                            e.preventDefault(); 
                            procesarArchivos(e.clipboardData.files);
                        }
                    }
                }
            });

            function procesarArchivos(files) {
                for (let i = 0; i < files.length; i++) {
                    let file = files[i];
                    if (!file.type.startsWith('image/')) continue;
                    let safeFile = new File([file], "captura_" + Date.now() + "_" + i + "." + file.type.split('/')[1], { type: file.type });
                    dataTransfer.items.add(safeFile);
                    
                    const reader = new FileReader();
                    reader.onload = e => {
                        const div = document.createElement('div');
                        div.className = 'preview-img-container';
                        div.innerHTML = `<img src="${e.target.result}">`;
                        previewContainer.appendChild(div);
                    };
                    reader.readAsDataURL(safeFile);
                }
                fileInput.files = dataTransfer.files;
                dropZone.querySelector('h5').innerText = dataTransfer.files.length + " fotos listas para subir";
            }
        });
    </script>
</body>
</html>