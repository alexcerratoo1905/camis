<?php

require_once "controllers/fichaProductoController.php";

include './includes/header.php';

?>

<main id="mainProducto" class="container my-5 py-5 mt-5" data-id="<?php echo $datosPrenda['id']; ?>"
    data-nombre="<?php echo $datosPrenda['nombre']; ?>"
    data-precio="<?php echo $datosPrenda['precio']; ?>"
    data-imagen=""
    data-color-prenda=""
    data-rebaja="<?php echo isset($datosPrenda['rebaja']) ? (int)$datosPrenda['rebaja'] : 0; ?>">
    <div class="row">

        <div class="col-md-6 mb-4 mb-md-0">
            <div class="d-flex flex-column-reverse flex-md-row gap-3 h-100">
                
                <div class="d-flex flex-row flex-md-column gap-2 overflow-auto pe-md-1 pb-2 pb-md-0" style="max-height: 80vh;">
                    <?php
                    $cont = 0;
                    foreach ($galeria as $img) {
                        $claseActiva = ($cont == 0) ? 'borde-activo' : '';
                        $colorId = isset($img['color_id']) ? $img['color_id'] : '';
                    ?>
                        <img src="<?php echo $img['url_imagen']; ?>"
                            class="miniatura-galeria miniatura-color <?php echo $claseActiva; ?>"
                            data-color-id="<?php echo $colorId; ?>"
                            style="width: 85px; height: 110px; object-fit: cover; cursor: pointer; flex-shrink: 0;"
                            onclick="cambiarFoto(this, '<?php echo $img['url_imagen']; ?>')"
                            alt="Miniatura">
                    <?php
                        $cont++;
                    }
                    ?>
                </div>

                <div class="position-relative flex-grow-1 bg-light overflow-hidden" style="min-height: 450px;">
                    <img id="imagenPrincipal" src="<?php echo $galeria[0]['url_imagen']; ?>" class="w-100 h-100 position-absolute" style="object-fit: cover; transition: opacity 0.3s ease-in-out;" alt="Prenda principal">
                    
                    <button class="btn position-absolute top-50 start-0 translate-middle-y ms-2 bg-white rounded-circle shadow-sm" style="width: 40px; height: 40px; z-index: 10;" onclick="cambiarConFlechas('prev')">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button class="btn position-absolute top-50 end-0 translate-middle-y me-2 bg-white rounded-circle shadow-sm" style="width: 40px; height: 40px; z-index: 10;" onclick="cambiarConFlechas('next')">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>

            </div>
        </div>

        <div class="col-md-6 ps-md-5 d-flex flex-column justify-content-center">

            <h1 class="display-5 fw-bold text-uppercase mb-2"><?php echo $datosPrenda["nombre"] ?> </h1>
            <div class="mb-4">
                <?php 
                $rebaja = isset($datosPrenda['rebaja']) ? (int)$datosPrenda['rebaja'] : 0;
                $precioFinal = $datosPrenda['precio'] - ($datosPrenda['precio'] * $rebaja / 100);
                
                if ($rebaja > 0){
                ?>
                    <span class="fs-4 text-muted text-decoration-line-through me-2"><?php echo number_format($datosPrenda['precio'], 2, ',', '.'); ?> €</span>
                    <span class="fs-3 fw-bold text-danger" id="precioFinalVisible"><?php echo number_format($precioFinal, 2, ',', '.'); ?> €</span>
                    <span class="badge bg-danger ms-2 mb-2 align-middle fs-6">-<?php echo $rebaja; ?>%</span>
                <?php } else{ ?>
                    <span class="fs-3 fw-light" id="precioFinalVisible"><?php echo number_format($datosPrenda['precio'], 2, ',', '.'); ?> €</span>
                <?php }; ?>
            </div>

            <div class="mb-4">
                <p class="text-muted text-uppercase" style="letter-spacing: 2px; font-size: 0.85rem;">Descripción</p>
                <p class="fs-6" style="line-height: 1.8;"><?php echo $datosPrenda["descripcion"] ?></p>
            </div>

            <form action="controllers/carritoController.php" method="POST" class="mt-auto">

                <input type="hidden" name="accion" value="agregar">
                <input type="hidden" name="idPrenda" value="<?php echo $datosPrenda['id']; ?>">
                <input type="hidden" name="color_id" id="input_color_id" value="">

                <?php if (!empty($coloresProducto)) { ?>
                    <div class="mb-4">
                        <label class="form-label text-uppercase m-0 mb-2" style="letter-spacing: 2px; font-size: 0.85rem;">Color / Equipación</label>
                        <div class="d-flex flex-wrap gap-2" id="contenedor-colores">
                            <?php foreach ($coloresProducto as $index => $color) {
                                $claseActivo = ($index == 0) ? 'border-dark' : 'border-light';
                            ?>
                                <div class="color-swatch-wrapper rounded-circle border border-2 <?php echo $claseActivo; ?>"
                                    style="cursor: pointer; transition: all 0.2s ease; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;"
                                    data-color-id="<?php echo $color['id']; ?>"
                                    onclick="seleccionarColor(<?php echo $color['id']; ?>, this)">

                                    <div class="color-swatch rounded-circle shadow-sm"
                                        style="background-color: <?php echo $color['valor_hexadecimal']; ?>; width: 26px; height: 26px;"
                                        title="<?php echo $color['nombre']; ?>">
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>

                <div class="row mb-3">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label for="version_genero" class="form-label text-uppercase m-0 mb-2" style="letter-spacing: 2px; font-size: 0.85rem;">Versión de la Prenda</label>
                        <select class="form-select border-dark rounded-0 py-2 fw-bold" id="version_genero" name="version_genero" onchange="actualizarGuiaTallas()">
                            <option value="hombre" selected>Versión Hombre</option>
                            <option value="mujer">Versión Mujer</option>
                            <option value="nino">Versión Niño / Kids</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label for="talla" class="form-label text-uppercase m-0" style="letter-spacing: 2px; font-size: 0.85rem;">Talla</label>
                            <a href="#" class="text-muted text-decoration-underline fw-bold" style="font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#modalGuiaTallas">Guía de tallas</a>
                        </div>
                        <select class="form-select border-dark rounded-0 py-2" id="talla" name="talla" required>
                            <option value="" selected disabled>Selecciona tu talla</option>
                            <option value="S">S</option>
                            <option value="M">M</option>
                            <option value="L">L</option>
                            <option value="XL">XL</option>
                            <option value="2XL">2XL (+1.00 €)</option>
                            <option value="3XL">3XL (+1.00 €)</option>
                            <option value="4XL">4XL (+1.00 €)</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4 p-3 bg-light border border-secondary border-opacity-25">
                    <label class="form-label text-uppercase m-0 mb-3 fw-bold" style="letter-spacing: 2px; font-size: 0.85rem;"><i class="bi bi-stars me-2"></i>Personalización y Extras</label>
                    
                    <div class="form-check mb-2">
                        <input class="form-check-input border-dark extra-checkbox" type="checkbox" name="extra_player" id="extra_player" value="1">
                        <label class="form-check-label fw-bold" for="extra_player">
                            Versión Player (+3.00 €) <span class="text-danger small ms-1">(1 talla más recomendada)</span>
                            <i class="bi bi-info-circle-fill ms-1 text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Mejora la calidad de la tela, detalles y estampados. Corte más ajustado. OJO: NO significa añadir el nombre de un jugador."></i>
                        </label>
                    </div>

                    <div class="form-check mb-2">
                        <input class="form-check-input border-dark extra-checkbox" type="checkbox" name="extra_pantalon" id="extra_pantalon" value="1">
                        <label class="form-check-label fw-bold" for="extra_pantalon">
                            Añadir Pantalón a juego (+10.00 €)
                        </label>
                    </div>

                    <div class="form-check mb-2">
                        <input class="form-check-input border-dark extra-checkbox" type="checkbox" name="tiene_parche" id="tiene_parche" value="1">
                        <label class="form-check-label fw-bold" for="tiene_parche">
                            Añadir Parches Oficiales (+1.00 €)
                        </label>
                    </div>
                    <div class="mb-3 ms-4 d-none animate__animated animate__fadeIn" id="div_texto_parche">
                        <input type="text" class="form-control form-control-sm border-dark" name="texto_parche" placeholder="Especifica qué parches (Ej: Champions League, LFP...)">
                    </div>

                    <div class="form-check mb-2">
                        <input class="form-check-input border-dark extra-checkbox" type="checkbox" name="tiene_personalizacion" id="tiene_personalizacion" value="1">
                        <label class="form-check-label fw-bold" for="tiene_personalizacion">
                            Añadir Nombre y Número (+2.00 €)
                        </label>
                    </div>
                    <div class="mb-2 ms-4 d-none animate__animated animate__fadeIn" id="div_texto_personalizacion">
                        <div class="row g-2">
                            <div class="col-8">
                                <input type="text" class="form-control form-control-sm border-dark text-uppercase" name="texto_nombre" placeholder="Nombre (Ej: MESSI)">
                            </div>
                            <div class="col-4">
                                <input type="number" class="form-control form-control-sm border-dark" name="texto_numero" placeholder="Nº (Ej: 10)">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3 mt-4 mb-2">

                    <button type="submit" class="add-to-cart flex-grow-1 m-0">
                        <span>Añadir al Carrito</span>
                        <svg class="morph" viewBox="0 0 64 13">
                            <path d="M0 12C6 12 17 12 32 12C47.9024 12 58 12 64 12V13H0V12Z" />
                        </svg>
                        <div class="shirt">
                            <svg class="first" viewBox="0 0 24 24">
                                <path d="M4.99997 3L8.99997 1.5C8.99997 1.5 10.6901 3 12 3C13.3098 3 15 1.5 15 1.5L19 3L22.5 8L19.5 10.5L19 9.5L17.1781 18.6093C17.062 19.1901 16.778 19.7249 16.3351 20.1181C15.4265 20.925 13.7133 22.3147 12 23C10.2868 22.3147 8.57355 20.925 7.66487 20.1181C7.22198 19.7249 6.93798 19.1901 6.82183 18.6093L4.99997 9.5L4.5 10.5L1.5 8L4.99997 3Z" />
                                <g>
                                    <path d="M16.3516 9.65383H14.3484V7.83652H14.1742V9.8269H16.5258V7.83652H16.3516V9.65383Z" />
                                    <path d="M14.5225 6.01934V7.66357H14.6967V7.4905H14.8186V7.66357H14.9928V6.01934H14.8186V7.31742H14.6967V6.01934H14.5225Z" />
                                    <path d="M14.1742 5.67319V7.66357H14.3484V5.84627H16.3516V7.66357H16.5258V5.67319H14.1742Z" />
                                    <path d="M15.707 9.48071H15.8812V9.28084L16.0032 9.4807V9.48071H16.1774V7.83648H16.0032V9.14683L15.8812 8.94697V7.83648H15.707V9.48071Z" />
                                    <path d="M15.5852 6.01931H15.1149V6.19238H15.5852V6.01931Z" />
                                    <path d="M15.707 6.01934V7.66357H15.8812V7.46371L16.0032 7.66357H16.1774V6.01934H16.0032V7.32969L15.8812 7.12984V6.01934H15.707Z" />
                                    <path d="M15.411 7.31742H15.2891V6.53857H15.411V7.31742ZM15.1149 7.66357H15.2891V7.4905H15.411V7.66357H15.5852V6.3655H15.1149V7.66357Z" />
                                    <path d="M14.5225 8.69756L14.8186 9.18291V9.30763H14.6967V9.13455H14.5225V9.48071H14.9928V9.13456V9.13455L14.6967 8.64917V8.00956H14.8186V8.6586H14.9928V7.83648H14.5225V8.69756Z" />
                                    <path d="M15.411 9.30763H15.2891V8.00956H15.411V9.30763ZM15.1149 9.48071H15.5852V7.83648H15.1149V9.48071Z" />
                                </g>
                            </svg>
                        </div>
                        <div class="cart">
                            <svg viewBox="0 0 36 26">
                                <path d="M1 2.5H6L10 18.5H25.5L28.5 7.5L7.5 7.5" class="shape" />
                                <path d="M11.5 25C12.6046 25 13.5 24.1046 13.5 23C13.5 21.8954 12.6046 21 11.5 21C10.3954 21 9.5 21.8954 9.5 23C9.5 24.1046 10.3954 25 11.5 25Z" class="wheel" />
                                <path d="M24 25C25.1046 25 26 24.1046 26 23C26 21.8954 25.1046 21 24 21C22.8954 21 22 21.8954 22 23C22 24.1046 22.8954 25 24 25Z" class="wheel" />
                                <path d="M14.5 13.5L16.5 15.5L21.5 10.5" class="tick" />
                            </svg>
                        </div>
                    </button>

                    <?php
                    $colorPorDefecto = !empty($coloresProducto) ? $coloresProducto['id'] : 0;
                    $iconoCorazon = 'bi-heart';
                    if (isset($arrayFavoritos) && in_array($datosPrenda['id'] . '-' . $colorPorDefecto, $arrayFavoritos)) {
                        $iconoCorazon = 'bi-heart-fill';
                    }
                    ?>
                    <button type="button"
                        id="btn-favorito-ficha"
                        class="btn btn-toggle-favorito btn-favorito-custom btn-favorito-lg d-flex justify-content-center align-items-center rounded-0"
                        data-id="<?php echo $datosPrenda['id']; ?>"
                        data-color="<?php echo $colorPorDefecto; ?>">
                        <i class="bi <?php echo $iconoCorazon ?> fs-4"></i>
                    </button>

                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modalGuiaTallas" tabindex="-1" aria-labelledby="modalGuiaTallasLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-dark rounded-0 shadow-lg">
                <div class="modal-header bg-dark text-white rounded-0">
                    <h5 class="modal-title text-uppercase fw-bold"><i class="bi bi-rulers me-2"></i>Guía de Tallas</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-4 bg-light">
                    <img id="imgGuiaTallas" src="public/img/tallas_hombre.jpg" class="img-fluid border border-secondary shadow-sm" alt="Guía de Tallas">
                    <p class="text-muted small mt-3 fw-bold"><i class="bi bi-info-circle me-1"></i>Las medidas son aproximadas y pueden variar ligeramente.</p>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    const listaFavoritosJS = <?php echo json_encode(isset($arrayFavoritos) ? $arrayFavoritos : []); ?>;

    // SCRIPT PARA CAMBIAR LA FOTO DE LA GUIA DE TALLAS SEGÚN LA VERSIÓN
    function actualizarGuiaTallas() {
        let version = document.getElementById('version_genero').value;
        let imgGuia = document.getElementById('imgGuiaTallas');
        
        if(imgGuia) {
            if (version === 'hombre') {
                imgGuia.src = 'public/img/tallas_hombre.jpg';
            } else if (version === 'mujer') {
                imgGuia.src = 'public/img/tallas_mujer.jpg';
            } else if (version === 'nino') {
                imgGuia.src = 'public/img/tallas_nino.jpg';
            }
        }
    }
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.4.0/gsap.min.js"></script>
<script src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/16327/MorphSVGPlugin3.min.js"></script>

<script src="public/js/producto.js?v=3"></script>

<?php
include './includes/prendasRecientes.php';
include './includes/footer.php';
?>