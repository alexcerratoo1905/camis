<?php 
require_once 'controllers/indexController.php'; 

// OBTENER PRODUCTOS DESTACADOS MANUALMENTE
$dbIndex = new Database();
$connIndex = $dbIndex->conectar();
$sqlDest = "SELECT p.*, 
            (SELECT color_id FROM producto_colores WHERE producto_id = p.id LIMIT 1) as color_id,
            (SELECT url_imagen FROM imagenes_productos WHERE producto_id = p.id AND es_principal = 1 LIMIT 1) as url_imagen
            FROM productos p 
            WHERE p.activo = 1 AND p.destacado = 1 AND p.es_segunda_mano = 0
            ORDER BY p.id DESC LIMIT 12";
$stmtDest = $connIndex->query($sqlDest);
$destacados = $stmtDest->fetchAll(PDO::FETCH_ASSOC);

include './includes/header.php'; 
?>

<!-- FONDO DINÁMICO 3D (Múltiples Camisetas) -->
<div class="scroll-shirt-container" id="shirt-container">
    <!-- Camiseta Principal (Centro) -->
    <img src="public/img/shirt-3d.png" class="floating-shirt" data-speed="0.5" data-rot="0.2" data-dir="1" style="top: 20%; left: 30%; width: 450px; z-index: 0; filter: brightness(0.9);">
    <!-- Camiseta Fondo Izquierda (Desenfocada) -->
    <img src="public/img/shirt-3d.png" class="floating-shirt" data-speed="0.2" data-rot="-0.15" data-dir="-1" style="top: 10%; left: 5%; width: 300px; z-index: -2; filter: blur(4px) brightness(0.7);">
    <!-- Camiseta Fondo Derecha (Desenfocada) -->
    <img src="public/img/shirt-3d.png" class="floating-shirt" data-speed="0.3" data-rot="0.3" data-dir="1" style="top: 50%; right: 5%; width: 350px; z-index: -1; filter: blur(2px) brightness(0.8);">
    <!-- Camiseta Abajo Izquierda -->
    <img src="public/img/shirt-3d.png" class="floating-shirt" data-speed="0.6" data-rot="-0.25" data-dir="-1" style="top: 75%; left: 15%; width: 250px; z-index: -1; filter: blur(1px) brightness(0.85);">
    <!-- Camiseta Abajo Derecha (Muy desenfocada y lenta) -->
    <img src="public/img/shirt-3d.png" class="floating-shirt" data-speed="0.15" data-rot="0.1" data-dir="1" style="top: 85%; right: 20%; width: 500px; z-index: -3; filter: blur(6px) brightness(0.6);">
</div>

<!-- HERO SECTION: Portada inicial -->
<section class="hero-section">
    <div class="container position-relative z-2">
        <h1 class="hero-title animate__animated animate__fadeInDown">DJALEXITO</h1>
        
        <a href="#bento-explorar" class="btn-scroll-down animate__animated animate__fadeIn animate__delay-2s mt-5 d-inline-block">
            <span class="d-block small text-uppercase fw-bold mb-2 text-muted" style="letter-spacing: 2px;">Descubre</span>
            <i class="bi bi-chevron-down fs-3"></i>
        </a>
    </div>
</section>

<!-- SECCIÓN BENTO GRID: Explorar Categorías -->
<section id="bento-explorar" class="container my-5 py-5 position-relative z-2">
    <div class="row g-4 bento-grid">
        
        <!-- Tarjeta Principal Ligas -->
        <div class="col-md-8">
            <a href="catalogo.php" class="bento-item bg-dark text-white d-flex align-items-end p-4 p-md-5 text-decoration-none shadow-lg h-100 position-relative overflow-hidden">
                <!-- Imagen de fondo rotada, difuminada y escalada -->
                <div class="bento-bg" style="background-image: url('public/img/shirt-3d.png'); filter: brightness(0.5); transform: scale(1.2) rotate(4deg);"></div>
                
                <div class="bento-content position-relative z-2 w-100">
                    <span class="badge bg-white text-dark mb-3 px-3 py-2 fw-bold" style="letter-spacing: 1px;">NUEVA TEMPORADA</span>
                    <h2 class="display-5 fw-bold text-uppercase mb-2 text-white">Equipaciones 26/27</h2>
                    <p class="fs-5 mb-0 text-white-50">Explora las últimas armaduras de La Liga y la Premier.</p>
                </div>
            </a>
        </div>
        
        <!-- Tarjeta Ediciones Especiales (Fondo Blanco) -->
        <div class="col-md-4">
            <a href="catalogo.php?tipo=1" class="bento-item bg-light text-dark d-flex align-items-end p-4 text-decoration-none shadow-lg h-100 position-relative overflow-hidden border border-secondary border-opacity-25">
                <!-- Imagen de fondo rotada y difuminada (con baja opacidad para que se vea blanco) -->
                <div class="bento-bg" style="background-image: url('public/img/shirt-3d.png'); transform: scale(1.4) rotate(-12deg); opacity: 0.3;"></div>
                
                <div class="bento-content position-relative z-2 w-100">
                    <h3 class="fw-bold text-uppercase mb-2 text-dark">Ediciones Especiales</h3>
                    <p class="small text-muted mb-0 fw-bold">Camisetas exclusivas y colaboraciones únicas de edición limitada.</p>
                </div>
            </a>
        </div>
        
        <!-- Tarjeta Retro (Fondo Amarillo) -->
        <div class="col-md-4">
            <a href="catalogo.php?coleccion=6" class="bento-item bg-warning text-dark d-flex align-items-end p-4 text-decoration-none shadow-lg h-100 position-relative overflow-hidden">
                <!-- Imagen de fondo rotada y difuminada (con baja opacidad para que el amarillo brille) -->
                <div class="bento-bg" style="background-image: url('public/img/shirt-3d.png'); transform: scale(1.3) rotate(8deg); opacity: 0.35;"></div>
                
                <div class="bento-content position-relative z-2 w-100">
                    <h3 class="fw-bold text-uppercase mb-1 text-dark">Retro Series</h3>
                    <p class="small text-dark mb-0 fw-bold">Clásicos que nunca mueren.</p>
                </div>
            </a>
        </div>
        
        <!-- Tarjeta Galería de Muestra -->
        <div class="col-md-8">
            <div class="bento-item bg-dark text-white p-4 p-md-5 shadow-lg h-100 position-relative overflow-hidden d-flex flex-column justify-content-center">
                <div class="position-relative z-2 w-100 text-center mb-4">
                    <h2 class="display-6 fw-bold text-uppercase mb-1">Nuestra Selección</h2>
                    <p class="text-white-50 mb-0">Calidad premium en cada detalle.</p>
                </div>
                <div class="d-flex justify-content-center align-items-center gap-3 position-relative z-2 bento-gallery">
                    <!-- FOTOS DE EJEMPLO -->
                    <img src="public/img/ejemplo1.jpg" onerror="this.onerror=null; this.src='public/img/shirt-3d.png';" class="rounded-3 shadow-lg bento-gallery-img" alt="Ejemplo 1">
                    <img src="public/img/ejemplo2.jpg" onerror="this.onerror=null; this.src='public/img/shirt-3d.png';" class="rounded-3 shadow-lg bento-gallery-img mt-4" alt="Ejemplo 2">
                    <img src="public/img/ejemplo3.jpg" onerror="this.onerror=null; this.src='public/img/shirt-3d.png';" class="rounded-3 shadow-lg bento-gallery-img" alt="Ejemplo 3">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SECCIÓN: CARRUSEL DESTACADOS (Cristal) -->
<section class="container my-5 py-5 position-relative z-2">
    <div class="glass-panel p-4 p-md-5">
        <div class="d-flex justify-content-between align-items-end mb-5 border-bottom border-dark pb-3">
            <h3 class="fw-bold text-uppercase m-0 display-6" style="letter-spacing: 2px;">Selección Destacada</h3>
            <a href="catalogo.php" class="text-dark fw-bold text-uppercase small text-decoration-none d-none d-md-block" style="letter-spacing: 1px;">Ver Todo <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
        
        <div id="carruselDestacados" class="carousel carousel-dark slide" data-bs-ride="carousel" data-bs-pause="hover">
            <div class="carousel-inner px-1 px-md-3" id="carruselDestacadosInner">
                <?php
                if (!empty($destacados)) {
                    $contador = 0;
                    foreach ($destacados as $prenda) {
                        if ($contador % 4 == 0) {
                            $claseActive = ($contador == 0) ? 'active' : '';
                ?>
                            <div class="carousel-item <?= $claseActive ?>" data-bs-interval="4000">
                                <div class="row">
                                <?php
                            }
                                ?>
                                <div class="col-6 col-md-3 position-relative d-flex flex-column mb-4">
                                    <div class="card product-card border-0 bg-transparent position-relative">
                                        <div class="img-wrapper position-relative overflow-hidden shadow-sm rounded-3">
                                            <a href="fichaProducto.php?idPrenda=<?= $prenda["id"] ?>&color=<?= $prenda["color_id"] ?>" class="text-decoration-none text-dark d-block">
                                                
                                                <?php
                                                $rebaja = isset($prenda['rebaja']) ? (int)$prenda['rebaja'] : 0;
                                                $precioFinal = $prenda['precio'] - ($prenda['precio'] * $rebaja / 100);
                                                
                                                if ($rebaja > 0){
                                                ?>
                                                    <span class="position-absolute top-0 end-0 m-2 badge bg-dark text-white rounded-0 fw-bold px-2 py-1 shadow-sm" style="font-size: 0.75rem; letter-spacing: 1px; z-index: 10;" >-<?= $rebaja; ?>%</span>
                                                <?php } ?>
                                                
                                                <img src="<?= $prenda['url_imagen'] ?>" class="card-img-top rounded-0 img-fluida-reciente" alt="<?= $prenda['nombre'] ?>">
                                            </a>
                                            <div id="overlay-tallas-<?= $prenda['id'] ?>" class="overlay-tallas d-none position-absolute bottom-0 start-0 w-100 bg-white bg-opacity-75 p-3 text-center">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="small fw-bold text-uppercase" style="letter-spacing: 1px;">Talla</span>
                                                    <button type="button" class="btn-close" style="font-size: 0.7rem;" onclick="cerrarOverlayTallas(event, <?= $prenda['id'] ?>)"></button>
                                                </div>
                                                <div id="contenedor-botones-<?= $prenda['id'] ?>" class="d-flex justify-content-center flex-wrap gap-2">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body text-center px-0 pb-1 mt-3">
                                            <a href="fichaProducto.php?idPrenda=<?= $prenda["id"] ?>&color=<?= $prenda["color_id"] ?>" class="text-decoration-none text-dark d-block">
                                                <h5 class="card-title text-uppercase fw-bold fs-6 mb-1 text-truncate" style="letter-spacing: 0.5px;"><?= $prenda['nombre'] ?></h5>
                                                
                                                <?php if ($rebaja > 0): ?>
                                                    <p class="card-text mb-0">
                                                        <span class="text-muted text-decoration-line-through small me-2"><?= number_format($prenda['precio'], 2) ?> €</span>
                                                        <span class="fw-bold text-dark"><?= number_format($precioFinal, 2) ?> €</span>
                                                    </p>
                                                <?php else: ?>
                                                    <p class="card-text mb-0 fw-bold text-dark"><?= number_format($prenda['precio'], 2) ?> €</p>
                                                <?php endif; ?>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between gap-2 mt-2 px-1">
                                        <button type="button" class="btn btn-outline-dark rounded-pill flex-grow-1 text-uppercase fw-bold"
                                            style="height: 38px; font-size: 0.7rem; letter-spacing: 1px; transition: all 0.3s;"
                                            onclick="abrirOverlayTallas(event, <?= $prenda['id'] ?>, <?= $prenda['color_id'] ?>)">
                                            Añadir <i class="bi bi-plus-lg ms-1"></i>
                                        </button>
                                        <?php
                                        $iconoCorazon = 'bi-heart';
                                        if (isset($arrayFavoritos) && in_array($prenda['id'] . '-' . $prenda['color_id'], $arrayFavoritos)) {
                                            $iconoCorazon = 'bi-heart-fill text-danger border-danger';
                                        }
                                        ?>
                                        <button type="button" class="btn btn-toggle-favorito btn-favorito-custom btn-favorito-std d-flex justify-content-center align-items-center rounded-circle"
                                            style="border-color: #000; width: 38px; height: 38px;"
                                            data-id="<?= $prenda['id'] ?>"
                                            data-color="<?= $prenda['color_id'] ?>">
                                            <i class="bi <?= $iconoCorazon ?>"></i>
                                        </button>
                                    </div>
                                </div>
                                <?php
                                $contador++;
                                if ($contador % 4 == 0 || $contador == count($destacados)) {
                                ?>
                                </div>
                            </div>
                    <?php
                                }
                            }
                        } else {
                    ?>
                    <div class="text-center py-5">
                        <i class="bi bi-stars display-4 text-muted d-block mb-3"></i>
                        <p class="text-muted fw-bold text-uppercase">Próximamente prendas destacadas...</p>
                    </div>
                <?php
                        }
                ?>
            </div>
            <!-- Controles minimalistas -->
            <button class="carousel-control-prev d-none d-md-flex" type="button" data-bs-target="#carruselDestacados" data-bs-slide="prev" style="width: 5%; justify-content: flex-start;">
                <i class="bi bi-arrow-left text-dark fs-2"></i>
            </button>
            <button class="carousel-control-next d-none d-md-flex" type="button" data-bs-target="#carruselDestacados" data-bs-slide="next" style="width: 5%; justify-content: flex-end;">
                <i class="bi bi-arrow-right text-dark fs-2"></i>
            </button>
        </div>
    </div>
</section>

<!-- SECCIÓN: CARRUSEL RECIENTES (Cristal) -->
<section class="position-relative z-2 mb-5 pb-5">
    <div class="container glass-panel p-4 p-md-5">
        <?php include './includes/prendasRecientes.php'; ?>
    </div>
</section>

<!-- Script para el efecto 3D Parallax -->
<script src="public/js/index.js"></script>

<?php include './includes/footer.php'; ?>