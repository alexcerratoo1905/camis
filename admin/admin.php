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
        #drop-zone { border: 2px dashed #343a40; border-radius: 8px; background-color: #f8f9fa; transition: all 0.3s ease; cursor: pointer; }
        #drop-zone:hover, #drop-zone.dragover { background-color: #e9ecef; border-color: #0dcaf0; }
        .preview-img-container { position: relative; display: inline-block; margin-right: 10px; margin-bottom: 10px; }
        .preview-img-container img { width: 100px; height: 100px; object-fit: cover; border-radius: 5px; border: 1px solid #ccc; }
        .crm-thumb-container { position: relative; display: inline-block; margin-right: 8px; margin-bottom: 8px; }
        .crm-thumb { width: 65px; height: 65px; object-fit: cover; border-radius: 4px; border: 1px solid #444; background-color: #fff; }
        .btn-borrar-foto { position: absolute; top: -5px; right: -5px; background: #dc3545; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; font-size: 12px; line-height: 1; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 1px 3px rgba(0,0,0,0.3); text-decoration: none; }
        .btn-borrar-foto:hover { background: #bd2130; color: white; }
        .nav-tabs .nav-link { color: #6c757d; border-radius: 0; }
        .nav-tabs .nav-link.active { color: #000; border-bottom: 3px solid #000; font-weight: bold; background-color: transparent; }
        
        /* Botón de guardado flotante e indestructible */
        .btn-flotante-guardar {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 9999;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            transition: transform 0.2s;
        }
        .btn-flotante-guardar:hover { transform: scale(1.05); }
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
                    </ul>
                    <hr class="mx-3 border-secondary">
                    <ul class="nav flex-column mb-2">
                        <li class="nav-item"><a class="nav-link admin-nav-link text-info" href="../index.php"><i class="bi bi-arrow-left-circle"></i> Volver a la Tienda</a></li>
                        <li class="nav-item"><a class="nav-link admin-nav-link text-danger" href="../controllers/usuarioController.php?accion=cerrar"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a></li>
                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 admin-content position-relative">

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
                    }
                    if ($msgTexto != "") {
                        echo '<div class="alert alert-success alert-dismissible fade show animate__animated animate__fadeIn" role="alert"><i class="bi bi-check-circle-fill me-2"></i> '.$msgTexto.'<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                    }
                }
                if (isset($_GET['error'])) {
                    echo '<div class="alert alert-danger alert-dismissible fade show animate__animated animate__shakeX" role="alert"><i class="bi bi-exclamation-triangle-fill me-2"></i> Ocurrió un error. Verifica que has rellenado todo bien.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                }
                ?>

                <div class="row">
                    <div class="col-12 pb-5">
                        <?php
                        switch ($seccion) {
                            case 'pedidos':
                                // [El código exacto que tenías de pedidos aquí - Se mantiene intacto para no romper nada]
                                // (Para acortar te lo resumo, usa tu código de pedidos que te funcionaba bien)
                                $listaPedidos = $pedido->listarPedidos();
                                // ...
                                break;

                            // =========================================================
                            // SECCIÓN DE PRODUCTOS (INVENTARIO MASIVO) CON TABS
                            // =========================================================
                            case 'productos':
                                $prod = new Producto($db->conectar());

                                $productosPorPagina = 5;
                                $paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
                                if ($paginaActual < 1) $paginaActual = 1;

                                $totalProductos = $prod->contarProductosPorTipo(false);
                                $totalPaginas = ceil($totalProductos / $productosPorPagina);
                                $offset = ($paginaActual - 1) * $productosPorPagina;

                                $listaInventario = $prod->listarProductosPaginados(false, $productosPorPagina, $offset);

                                // AGRUPAMOS POR PRODUCTO, Y DENTRO TODAS SUS VARIANTES (Local, Visitante...)
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
                        ?>
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h3 class="fw-bold m-0 text-uppercase">Gestión de Inventario</h3>
                                    <button class="btn btn-admin-black px-3 py-2" type="button" data-bs-toggle="collapse" data-bs-target="#formNuevaPrenda">
                                        <i class="bi bi-plus-lg me-2"></i> Crear Producto Nuevo
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

                                    <?php if (empty($productosAgrupados)) { ?>
                                        <div class="alert alert-secondary text-center py-5">No se han encontrado productos.</div>
                                    <?php } else { ?>
                                        <?php 
                                        foreach ($productosAgrupados as $id => $datos) { 
                                            if ($datos['es_segunda_mano'] == 1) continue;
                                            
                                            // Traemos la descripción del producto base
                                            $stmtDesc = $conexion->prepare("SELECT descripcion FROM productos WHERE id = ?");
                                            $stmtDesc->execute([$id]);
                                            $descReal = $stmtDesc->fetchColumn();
                                        ?>
                                            <div class="card mb-5 border-0 shadow-sm admin-card" style="border-left: 6px solid #0dcaf0;">
                                                <div class="card-header bg-dark text-white py-3">
                                                    <div class="row align-items-center g-3">
                                                        <div class="col-12 col-lg-4">
                                                            <div class="d-flex align-items-center gap-1 mb-1">
                                                                <span class="text-secondary fw-bold small">#<?php echo $id; ?></span>
                                                                <input type="text" name="nombre[<?php echo $id; ?>]" value="<?php echo htmlspecialchars($datos['nombre']); ?>" class="form-control form-control-sm border-0 bg-secondary text-white fw-bold text-uppercase w-100" style="letter-spacing: 0.5px;" required>
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

                                                        <div class="col-6 col-md-3 col-lg-2">
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
                                                                <input type="text" name="descripcion[<?php echo $id; ?>]" value="<?php echo htmlspecialchars($descReal ?? ''); ?>" class="form-control bg-dark text-white border-0 small" placeholder="Descripción breve de la camiseta">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="card-body bg-white border border-top-0 border-light p-3">
                                                    
                                                    <ul class="nav nav-tabs border-bottom mb-3" role="tablist">
                                                        <?php 
                                                        $vIndex = 0;
                                                        foreach($datos['variantes'] as $color_id => $var): 
                                                            $isActive = ($vIndex == 0) ? 'active' : '';
                                                        ?>
                                                        <li class="nav-item" role="presentation">
                                                            <button class="nav-link text-uppercase <?= $isActive ?>" data-bs-toggle="tab" data-bs-target="#variante-<?= $id ?>-<?= $color_id ?>" type="button" role="tab">
                                                                <i class="bi bi-tag-fill me-1"></i> <?= htmlspecialchars($var['equipacion']) ?>
                                                            </button>
                                                        </li>
                                                        <?php $vIndex++; endforeach; ?>
                                                        
                                                        <li class="nav-item ms-auto">
                                                            <button type="button" class="btn btn-sm btn-warning fw-bold text-dark mt-1" data-bs-toggle="modal" data-bs-target="#modalVariante<?= $id ?>">
                                                                <i class="bi bi-plus-circle-fill"></i> Añadir Variante
                                                            </button>
                                                        </li>
                                                    </ul>

                                                    <div class="tab-content">
                                                        <?php 
                                                        $vIndex = 0;
                                                        foreach($datos['variantes'] as $color_id => $var): 
                                                            $isActive = ($vIndex == 0) ? 'show active' : '';
                                                            
                                                            $stmtFotos = $conexion->prepare("SELECT id, url_imagen FROM imagenes_productos WHERE producto_id = ? AND color_id = ?");
                                                            $stmtFotos->execute([$id, $color_id]);
                                                            $fotosProducto = $stmtFotos->fetchAll(PDO::FETCH_ASSOC);
                                                        ?>
                                                        <div class="tab-pane fade <?= $isActive ?>" id="variante-<?= $id ?>-<?= $color_id ?>" role="tabpanel">
                                                            <div class="d-flex flex-wrap align-items-center bg-light p-3 rounded">
                                                                <?php foreach ($fotosProducto as $ft) { ?>
                                                                    <div class="crm-thumb-container">
                                                                        <img src="../<?= htmlspecialchars($ft['url_imagen']); ?>" class="crm-thumb shadow-sm">
                                                                        <a href="../controllers/adminController.php?accion=borrarFotoEspecifica&id_foto=<?= $ft['id']; ?>&p_id=<?= $id; ?>&pag=<?= $paginaActual; ?>" class="btn-borrar-foto" onclick="return confirm('¿Borrar foto?');">×</a>
                                                                    </div>
                                                                <?php } ?>
                                                                
                                                                <button type="button" class="btn btn-outline-secondary border-dashed ms-2 bg-white shadow-sm" style="height: 65px; width: 65px; border-style: dashed; border-width: 2px;" onclick="document.getElementById('add-foto-input-<?= $id ?>-<?= $color_id ?>').click();" title="Añadir foto">
                                                                    <i class="bi bi-plus-lg fs-4"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <?php $vIndex++; endforeach; ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal fade" id="modalVariante<?php echo $id; ?>" tabindex="-1">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-dark rounded-0 shadow-lg">
                                                        <div class="modal-header bg-warning text-dark rounded-0">
                                                            <h5 class="modal-title fw-bold text-uppercase"><i class="bi bi-plus-circle-fill me-2"></i>Añadir Nueva Variante</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        </div>
                                                </div>
                                            </div>
                                            
                                        <?php } ?>
                                    <?php } ?>

                                    <div class="d-flex justify-content-center mt-4">
                                        <nav aria-label="Paginación de inventario">
                                            <ul class="pagination mb-0 shadow-sm">
                                                <?php $disabledPrev = ($paginaActual <= 1) ? 'disabled' : ''; ?>
                                                <li class="page-item <?php echo $disabledPrev; ?>"><a class="page-link text-dark" href="admin.php?seccion=productos&pagina=<?= $paginaActual - 1 ?>">Anterior</a></li>
                                                <?php for ($i = 1; $i <= $totalPaginas; $i++) { $activa = ($i == $paginaActual) ? 'active bg-dark border-dark text-white' : 'text-dark'; ?>
                                                    <li class="page-item"><a class="page-link <?php echo $activa; ?>" href="admin.php?seccion=productos&pagina=<?= $i ?>"><?php echo $i; ?></a></li>
                                                <?php } ?>
                                                <?php $disabledNext = ($paginaActual >= $totalPaginas) ? 'disabled' : ''; ?>
                                                <li class="page-item <?php echo $disabledNext; ?>"><a class="page-link text-dark" href="admin.php?seccion=productos&pagina=<?= $paginaActual + 1 ?>">Siguiente</a></li>
                                            </ul>
                                        </nav>
                                    </div>

                                    <div class="btn-flotante-guardar d-none d-md-block">
                                        <button type="submit" class="btn btn-dark btn-lg shadow-lg fw-bold px-5 py-3 rounded-pill border border-2 border-light text-uppercase ls-1">
                                            <i class="bi bi-save-fill fs-5 me-2"></i> Guardar Cambios
                                        </button>
                                    </div>
                                    <div class="d-block d-md-none position-fixed bottom-0 start-0 w-100 p-3 bg-white border-top shadow-lg" style="z-index: 9999;">
                                        <button type="submit" class="btn btn-dark btn-lg shadow fw-bold w-100 text-uppercase">
                                            <i class="bi bi-save-fill me-2"></i> Guardar Cambios
                                        </button>
                                    </div>

                                </form>

                                <?php foreach ($productosAgrupados as $id => $datos) { 
                                    foreach($datos['variantes'] as $color_id => $var) { ?>
                                    <form action="../controllers/adminController.php" method="POST" enctype="multipart/form-data" class="d-none">
                                        <input type="hidden" name="accion" value="anadirFotosGaleriaExistente">
                                        <input type="hidden" name="producto_id" value="<?= $id ?>">
                                        <input type="hidden" name="color_id" value="<?= $color_id ?>">
                                        <input type="hidden" name="pagina_retorno" value="<?= $paginaActual ?>">
                                        <input type="file" id="add-foto-input-<?= $id ?>-<?= $color_id ?>" name="imagenes[]" onchange="this.form.submit();" multiple>
                                    </form>
                                <?php } } ?>

                                <?php foreach ($productosAgrupados as $id => $datos) { ?>
                                    <div class="d-none">
                                        <div id="form-variante-<?= $id ?>">
                                            <form action="../controllers/adminController.php" method="POST" enctype="multipart/form-data">
                                                <div class="modal-body p-4">
                                                    <input type="hidden" name="accion" value="anadirEquipacionExtra">
                                                    <input type="hidden" name="producto_id" value="<?php echo $id; ?>">
                                                    <input type="hidden" name="pagina_retorno" value="<?php echo $paginaActual; ?>">
                                                    
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
                                    <script>
                                        // Truco JS para inyectar el form en el modal sin romper el form masivo exterior
                                        document.getElementById('modalVariante<?= $id ?>').querySelector('.modal-content').innerHTML += document.getElementById('form-variante-<?= $id ?>').innerHTML;
                                    </script>
                                <?php } ?>

                        <?php break; ?>
                        <?php } ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sistema Dropzone (El que ya funcionaba perfecto)
        document.addEventListener("DOMContentLoaded", function() {
            const dropZone = document.getElementById('drop-zone');
            if (!dropZone) return;
            const fileInput = document.getElementById('file-input');
            const previewContainer = document.getElementById('preview-container');
            const dataTransfer = new DataTransfer();

            dropZone.addEventListener('click', () => fileInput.click());
            fileInput.addEventListener('change', (e) => procesarArchivos(e.target.files));
            
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