<?php 
require_once 'controllers/catalogoController.php'; 
include './includes/header.php'; 
?>
<main class="container my-5 py-5 mt-5">
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-5 fw-bold text-uppercase" style="letter-spacing: 4px;">Catálogo</h1>
            <h2><?php echo $mensajeFiltrado ?></h2>
            <p class="text-muted">Descubre todas nuestras colecciones</p>
        </div>
    </div>
    <div class="row">
        
        <!-- Botón Móvil Filtros -->
        <div class="col-12 d-lg-none mb-3">
            <button class="btn btn-outline-dark w-100 fw-bold text-uppercase rounded-0 py-3 d-flex justify-content-between align-items-center" type="button" data-bs-toggle="offcanvas" data-bs-target="#filtrosOffcanvas">
                <span><i class="bi bi-sliders me-2"></i> Filtrar y Ordenar</span>
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>
        <aside class="col-lg-3 mb-4">
            <div class="offcanvas-lg offcanvas-start border-0 shadow-sm" tabindex="-1" id="filtrosOffcanvas" aria-labelledby="filtrosOffcanvasLabel">
                
                <div class="offcanvas-header border-bottom bg-light d-lg-none">
                    <h5 class="offcanvas-title fw-bold text-uppercase m-0" id="filtrosOffcanvasLabel" style="letter-spacing: 2px;">Filtros</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" data-bs-target="#filtrosOffcanvas" aria-label="Cerrar"></button>
                </div>
                <div class="offcanvas-body p-0 p-lg-0 flex-column bg-white">
                    <div class="sticky-top w-100" style="top: 100px; z-index: 1;">
                        
                        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2 p-3 p-lg-0">
                            <h5 class="fw-bold text-uppercase m-0 d-none d-lg-block">Filtros</h5>
                            <?php if(!$esModoSecreto){ ?>
                                <a href="catalogo.php" class="text-muted small text-decoration-underline fw-bold">Limpiar <span class="d-none d-lg-inline">todo</span></a>
                            <?php } else { ?>
                                <a href="catalogo.php?especial=herror" class="text-muted small text-decoration-underline fw-bold">Limpiar <span class="d-none d-lg-inline">todo</span></a>
                            <?php } ?>
                        </div>
                        <div class="accordion accordion-flush w-100" id="acordeonFiltros">
                            
                            <?php if (!$esModoSecreto) { ?>
                                <a href="<?php echo crearUrl('rebajas', '1'); ?>" class="list-group-item list-group-item-action fw-bold text-danger text-uppercase px-3 px-lg-0 mb-2 border-bottom pb-3" style="letter-spacing: 1px;">
                                    <i class="bi bi-tag-fill me-2"></i> Rebajas
                                </a>
                            <?php } ?>
                            <!-- 1. ORDENAR POR -->
                            <div class="accordion-item bg-transparent border-bottom">
                                <h2 class="accordion-header">
                                    <button class="accordion-button bg-transparent px-3 px-lg-0 fw-bold text-uppercase shadow-none" style="font-size: 0.9rem;" type="button" data-bs-toggle="collapse" data-bs-target="#filtroOrdenar">
                                        Ordenar por
                                    </button>
                                </h2>
                                <div id="filtroOrdenar" class="accordion-collapse collapse show" data-bs-parent="#acordeonFiltros">
                                    <div class="accordion-body px-3 px-lg-0 py-2">
                                        <ul class="list-unstyled mb-0">
                                            <li class="mb-2"><a href="<?php echo crearUrl('orden', 'precioAsc'); ?>" class="text-muted nav-filtro transicion-suave">Precio: Menor a Mayor</a></li>
                                            <li class="mb-2"><a href="<?php echo crearUrl('orden', 'precioDesc'); ?>" class="text-muted nav-filtro transicion-suave">Precio: Mayor a Menor</a></li>
                                            <li class="mb-2"><a href="<?php echo crearUrl('orden', 'nombreAsc'); ?>" class="text-muted nav-filtro transicion-suave">Alfabéticamente: A - Z</a></li>
                                            <li class="mb-2"><a href="<?php echo crearUrl('orden', 'nombreDesc'); ?>" class="text-muted nav-filtro transicion-suave">Alfabéticamente: Z - A</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- 2. CATEGORÍA -->
                            <?php if (!$esModoSecreto) { ?>
                                <div class="accordion-item bg-transparent border-bottom">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-transparent px-3 px-lg-0 fw-bold text-uppercase shadow-none" style="font-size: 0.9rem;" type="button" data-bs-toggle="collapse" data-bs-target="#filtroColeccion">
                                            Categoría
                                        </button>
                                    </h2>
                                    <div id="filtroColeccion" class="accordion-collapse collapse" data-bs-parent="#acordeonFiltros">
                                        <div class="accordion-body px-3 px-lg-0 py-2">
                                            <ul class="list-unstyled mb-0">
                                                <?php foreach ($listaCategorias as $categoria) { ?>
                                                    <li class="mb-2"><a href="<?php echo crearUrl('coleccion', $categoria['id']); ?>" class="text-muted nav-filtro transicion-suave"><?php echo $categoria["nombre"] ?></a></li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <!-- 3. TIPO DE PRENDA -->
                            <div class="accordion-item bg-transparent border-bottom">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed bg-transparent px-3 px-lg-0 fw-bold text-uppercase shadow-none" style="font-size: 0.9rem;" type="button" data-bs-toggle="collapse" data-bs-target="#filtroCategoria">
                                        Tipo de prenda
                                    </button>
                                </h2>
                                <div id="filtroCategoria" class="accordion-collapse collapse" data-bs-parent="#acordeonFiltros">
                                    <div class="accordion-body px-3 px-lg-0 py-2">
                                        <ul class="list-unstyled mb-0">
                                            <li class="mb-2"><a href="<?php echo crearUrl('tipo', 1); ?>" class="text-muted nav-filtro transicion-suave">Camisetas</a></li>
                                            <li class="mb-2"><a href="<?php echo crearUrl('tipo', 2); ?>" class="text-muted nav-filtro transicion-suave">Entrenamiento</a></li>
                                            <li class="mb-2"><a href="<?php echo crearUrl('tipo', 3); ?>" class="text-muted nav-filtro transicion-suave">Chándal</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- TALLA -->
                            <div class="accordion-item bg-transparent border-bottom">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed bg-transparent px-3 px-lg-0 fw-bold text-uppercase shadow-none" style="font-size: 0.9rem;" type="button" data-bs-toggle="collapse" data-bs-target="#filtroTalla">
                                        Talla
                                    </button>
                                </h2>
                                <div id="filtroTalla" class="accordion-collapse collapse" data-bs-parent="#acordeonFiltros">
                                    <div class="accordion-body px-3 px-lg-0 py-2">
                                        <div class="d-flex flex-wrap gap-2">
                                            <a href="<?php echo crearUrl('talla', 'S'); ?>" class="border text-muted text-decoration-none px-3 py-1 nav-filtro transicion-suave">S</a>
                                            <a href="<?php echo crearUrl('talla', 'M'); ?>" class="border text-muted text-decoration-none px-3 py-1 nav-filtro transicion-suave">M</a>
                                            <a href="<?php echo crearUrl('talla', 'L'); ?>" class="border text-muted text-decoration-none px-3 py-1 nav-filtro transicion-suave">L</a>
                                            <a href="<?php echo crearUrl('talla', 'XL'); ?>" class="border text-muted text-decoration-none px-3 py-1 nav-filtro transicion-suave">XL</a>
                                            <a href="<?php echo crearUrl('talla', '2XL'); ?>" class="border text-muted text-decoration-none px-3 py-1 nav-filtro transicion-suave">2XL</a>
                                            <a href="<?php echo crearUrl('talla', '3XL'); ?>" class="border text-muted text-decoration-none px-3 py-1 nav-filtro transicion-suave">3XL</a>
                                            <a href="<?php echo crearUrl('talla', '4XL'); ?>" class="border text-muted text-decoration-none px-3 py-1 nav-filtro transicion-suave">4XL</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- COLOR -->
                            <div class="accordion-item bg-transparent border-bottom">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed bg-transparent px-3 px-lg-0 fw-bold text-uppercase shadow-none" style="font-size: 0.9rem;" type="button" data-bs-toggle="collapse" data-bs-target="#filtroColor">
                                        Color
                                    </button>
                                </h2>
                                <div id="filtroColor" class="accordion-collapse collapse" data-bs-parent="#acordeonFiltros">
                                    <div class="accordion-body px-3 px-lg-0 py-2">
                                        <div class="d-flex flex-wrap gap-2">
                                            <?php foreach ($listaColores as $color) { ?>
                                                <a href="<?php echo crearUrl('color', $color["nombre"]); ?>" class="color-swatch border border-dark" style="background-color: <?php echo $color["valor_hexadecimal"] ?>;" title="<?php echo $color["nombre"] ?>"></a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- PRECIO -->
                            <div class="accordion-item bg-transparent border-bottom">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed bg-transparent px-3 px-lg-0 fw-bold text-uppercase shadow-none" style="font-size: 0.9rem;" type="button" data-bs-toggle="collapse" data-bs-target="#filtroPrecio">
                                        Precio
                                    </button>
                                </h2>
                                <div id="filtroPrecio" class="accordion-collapse collapse" data-bs-parent="#acordeonFiltros">
                                    <div class="accordion-body px-3 px-lg-0 py-3">
                                        <div class="range-slider-container position-relative mb-3 mt-4">
                                            <div class="slider-track"></div>
                                            <input type="range" min="<?php echo $precioMin; ?>" max="<?php echo $precioMax; ?>" value="<?php echo $precioMin; ?>" id="slider-min" class="custom-range">
                                            <input type="range" min="<?php echo $precioMin; ?>" max="<?php echo $precioMax; ?>" value="<?php echo $precioMax; ?>" id="slider-max" class="custom-range">
                                        </div>
                                        <div class="d-flex justify-content-between text-muted small fw-bold mb-3">
                                            <span>Min: <span id="precio-min-val"><?php echo $precioMin ?></span> €</span>
                                            <span>Max: <span id="precio-max-val"><?php echo $precioMax ?></span> €</span>
                                        </div>
                                        <button class="btn btn-dark w-100 btn-sm text-uppercase" onclick="aplicarFiltroPrecio()">Aplicar Filtro</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
        <!-- SECCIÓN DE PRODUCTOS -->
        <section class="col-lg-9">
            <div class="row g-4">
                <?php
                if (!empty($productosPagina)) {
                    foreach ($productosPagina as $prenda) {
                        $listaImagenesColor = $imagen->listarImagenesPorColor($prenda["id"], $prenda["color_id"]);
                        $fotoHover = count($listaImagenesColor) > 1 ? $listaImagenesColor[1]["url_imagen"] : $prenda["url_imagen"];
                ?>
                        <div class="col-6 col-md-4">
                            <div class="card product-card border-0 bg-transparent h-100 position-relative d-flex flex-column">
                                <a href="fichaProducto.php?idPrenda=<?php echo $prenda["id"] ?>&color=<?php echo $prenda['color_id']; ?>" class="text-decoration-none text-dark d-block">
                                    <?php
                                    $tieneRebaja = isset($prenda['rebaja']) && $prenda['rebaja'] > 0;
                                    $precioFinal = $prenda['precio'];
                                    if ($tieneRebaja) {
                                        $precioFinal = $prenda['precio'] - ($prenda['precio'] * ($prenda['rebaja'] / 100));
                                    }
                                    ?>
                                    <div class="img-wrapper position-relative overflow-hidden shadow-sm rounded-3">
                                        <img src="<?php echo $prenda["url_imagen"]; ?>" class="card-img-top img-principal transicion-suave rounded-3" alt="Prenda">
                                        <img src="<?php echo $fotoHover; ?>" class="card-img-top img-hover transicion-suave position-absolute top-0 start-0 w-100 h-100 rounded-3" alt="Prenda Hover">
                                        
                                        <?php if ($tieneRebaja): ?>
                                            <span class="position-absolute top-0 end-0 m-2 badge bg-dark text-white rounded-0 fw-bold px-2 py-1 shadow-sm" style="font-size: 0.75rem; letter-spacing: 1px; z-index: 10;">
                                                -<?= $prenda['rebaja'] ?>%
                                            </span>
                                        <?php endif; ?>
                                        <div id="overlay-tallas-<?= $prenda['id'] ?>" class="overlay-tallas d-none position-absolute bottom-0 start-0 w-100 bg-white bg-opacity-75 p-3 text-center" style="z-index: 20;" onclick="event.preventDefault();">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="small fw-bold text-uppercase" style="letter-spacing: 1px;">Talla</span>
                                                <button type="button" class="btn-close" style="font-size: 0.7rem;" onclick="cerrarOverlayTallas(event, <?= $prenda['id'] ?>)"></button>
                                            </div>
                                            <div id="contenedor-botones-<?= $prenda['id'] ?>" class="d-flex justify-content-center flex-wrap gap-2">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body text-center px-0 pb-1 mt-3">
                                        <h5 class="card-title text-uppercase fw-bold fs-6 mb-1 text-truncate" style="letter-spacing: 0.5px;"><?php echo $prenda["nombre"] ?></h5>
                                        <?php if ($tieneRebaja): ?>
                                            <p class="card-text mb-0">
                                                <del class="text-muted small me-2"><?= number_format($prenda['precio'], 2) ?> €</del>
                                                <span class="text-dark fw-bold fs-6"><?= number_format($precioFinal, 2) ?> €</span>
                                            </p>
                                        <?php else: ?>
                                            <p class="card-text mb-0 fw-bold text-dark"><?php echo number_format($prenda["precio"], 2) ?> €</p>
                                        <?php endif; ?>
                                    </div>
                                </a>
                                <!-- BOTONES ESTANDARIZADOS REDONDOS -->
                                <div class="d-flex align-items-center justify-content-between gap-2 mt-2 px-1">
                                    <button type="button" class="btn btn-outline-dark rounded-pill flex-grow-1 text-uppercase fw-bold"
                                        style="height: 38px; font-size: 0.7rem; letter-spacing: 1px; transition: all 0.3s;"
                                        onclick="abrirOverlayTallas(event, <?= $prenda['id'] ?>, <?= $prenda['color_id'] ?>)">
                                        Añadir <i class="bi bi-plus-lg ms-1"></i>
                                    </button>
                                    
                                    <?php
                                    $iconoCorazon = 'bi-heart';
                                    if (isset($arrayFavoritos) && in_array($prenda['id'] . '-' . $prenda['color_id'], $arrayFavoritos)) {
                                        $iconoCorazon = 'bi-heart-fill text-danger';
                                    }
                                    ?>
                                    <button type="button" class="btn btn-toggle-favorito btn-favorito-custom btn-favorito-std d-flex justify-content-center align-items-center rounded-circle m-0"
                                        style="border-color: #000; width: 38px; height: 38px;"
                                        data-id="<?= $prenda['id'] ?>"
                                        data-color="<?= $prenda['color_id'] ?>">
                                        <i class="bi <?= $iconoCorazon ?>"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                <?php
                    }
                } else {
                    echo "<p class='text-center w-100 py-5 text-muted'>No se han encontrado prendas con estos filtros.</p>";
                }
                ?>
            </div>
            <?php if ($totalPaginas > 1): ?>
                <div class="row mt-5 pt-3 border-top">
                    <div class="col-12 d-flex justify-content-center">
                        <nav aria-label="Navegación del catálogo">
                            <ul class="pagination mb-0 shadow-sm">
                                
                                <?php $disabledPrev = ($paginaActual <= 1) ? 'disabled' : ''; ?>
                                <li class="page-item <?php echo $disabledPrev; ?>">
                                    <a class="page-link text-dark rounded-0 border-dark" href="<?php echo $paginaActual > 1 ? crearUrlPaginacion($paginaActual - 1) : '#'; ?>" aria-label="Anterior">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                
                                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                                    <li class="page-item">
                                        <a class="page-link rounded-0 border-dark <?php echo ($paginaActual == $i) ? 'bg-dark text-white' : 'text-dark'; ?>" href="<?php echo crearUrlPaginacion($i); ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php $disabledNext = ($paginaActual >= $totalPaginas) ? 'disabled' : ''; ?>
                                <li class="page-item <?php echo $disabledNext; ?>">
                                    <a class="page-link text-dark rounded-0 border-dark" href="<?php echo $paginaActual < $totalPaginas ? crearUrlPaginacion($paginaActual + 1) : '#'; ?>" aria-label="Siguiente">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            <?php endif; ?>
        </section>
    </div>
</main>
<script src="public/js/catalogo.js"></script>
<?php 
include './includes/prendasRecientes.php';
include './includes/footer.php'; 
?>