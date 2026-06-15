<?php

require_once 'controllers/segundaManoController.php';

include './includes/header.php';

?>

<main class="container my-5 py-5 min-vh-100">

    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold text-uppercase" style="letter-spacing: 2px;">Segunda Mano</h1>
        <p class="text-muted fs-5">Dale una segunda vida a tu ropa o encuentra joyas vintage únicas.</p>
    </div>

    <div class="text-center mb-5">
        <button type="button" class="btn btn-principal btn-lg text-uppercase fw-bold px-5 py-3 rounded-0" data-bs-toggle="modal" data-bs-target="#modalSubirPrenda" style="letter-spacing: 1px;">
            Vender mi prenda <i class="bi bi-tag-fill ms-2"></i>
        </button>
    </div>

    <div class="modal fade" id="modalSubirPrenda" tabindex="-1" aria-labelledby="modalSubirPrendaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-0 border-0 shadow">

                <div class="modal-header border-bottom-0 pb-0 mt-3 px-4">
                    <h5 class="modal-title fw-bold text-uppercase fs-4" id="modalSubirPrendaLabel">Detalles de la prenda</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-4 pb-4">
                    <form action="controllers/subirPrendaSegundaManoController.php" method="POST" enctype="multipart/form-data">

                        <div class="mb-3">
                            <label for="nombrePrenda" class="form-label fw-bold small text-uppercase text-muted">Título</label>
                            <input type="text" class="form-control rounded-0 p-2" id="nombrePrenda" name="nombrePrenda" required>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label for="precioPrenda" class="form-label fw-bold small text-uppercase text-muted">Precio (€)</label>
                                <input type="number" step="0.01" class="form-control rounded-0 p-2 shadow-none border-dark" id="precioPrenda" name="precioPrenda" placeholder="0.00" required>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="tallaPrenda" class="form-label fw-bold small text-uppercase text-muted">Talla</label>
                                <select class="form-select rounded-0 p-2 shadow-none border-dark" id="tallaPrenda" name="tallaPrenda" required>
                                    <option value="" selected disabled>Elegir...</option>
                                    <?php foreach ($listaTallas as $talla) { ?>
                                        <option value="<?php echo $talla['talla']; ?>"><?php echo $talla['talla']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="colorPrenda" class="form-label fw-bold small text-uppercase text-muted">Color</label>
                            <select class="form-select rounded-0 p-2" id="colorPrenda" name="colorPrenda" required>
                                <option value="" selected disabled>Elegir...</option>
                                <?php foreach ($listaColores as $color) { ?>

                                    <option value="<?php echo $color['id']; ?>"><?php echo $color['nombre']; ?></option>

                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="colorPrenda" class="form-label fw-bold small text-uppercase text-muted">Tipo prenda</label>
                            <select class="form-select rounded-0 p-2" id="tipoPrenda" name="tipoPrenda" required>
                                <option value="" selected disabled>Elegir...</option>
                                <?php foreach ($listaTipoPrenda as $tipoPrendda) { ?>

                                    <option value="<?php echo $tipoPrendda['id']; ?>"><?php echo $tipoPrendda['nombre']; ?></option>

                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-4 mt-2">
                            <label for="fotoPrenda" class="form-label fw-bold small text-uppercase text-muted">Foto del artículo</label>
                            <input class="form-control rounded-0" type="file" id="fotoPrenda" name="foto" accept="image/*" required>
                            <div class="form-text" style="font-size: 0.75rem;">Sube una foto clara y con buena iluminación.</div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-principal rounded-0 py-3 text-uppercase fw-bold" style="letter-spacing: 1px;">
                                Subir prenda
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-5 pt-5 border-top border-dark border-2">
        <h2 class="fw-bold text-uppercase mb-4">Recién llegados</h2>

        <?php if (empty($catalogoSegundaMano)) { ?>
            <div class="text-center p-5 bg-light border border-dark border-1">
                <i class="bi bi-emoji-frown display-1 text-muted mb-3 d-block"></i>
                <h4 class="fw-bold text-uppercase">Aún no hay prendas disponibles</h4>
                <p class="text-muted">¡Sé el primero en subir ropa a nuestro catálogo de segunda mano!</p>
            </div>
        <?php } else { ?>
            <div class="row g-4">
                <?php foreach ($catalogoSegundaMano as $prenda) { ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card product-card border-0 bg-transparent h-100 position-relative d-flex flex-column">

                            <a href="fichaProducto.php?idPrenda=<?= $prenda['id'] ?>&color=<?= $prenda['color_id'] ?>" class="text-decoration-none text-dark d-block flex-grow-1">

                                <div class="position-relative overflow-hidden border border-dark border-1 img-contenedor-sm">
                                    <img src="<?= !empty($prenda['url_imagen']) ? $prenda['url_imagen'] : 'public/img/fondo.jpg' ?>"
                                        class="w-100 h-100" alt="<?= $prenda['nombre'] ?>" style="object-fit: cover;">

                                    <span class="position-absolute top-0 end-0 m-2 badge bg-dark text-white rounded-0 fw-bold px-2 py-1" style="font-size: 0.7rem; letter-spacing: 1px;">
                                        2ª MANO
                                    </span>
                                </div>

                                <div class="card-body px-0 pb-1 mt-2 text-center">
                                    <h6 class="card-title text-uppercase fw-bold mb-1 text-truncate" style="font-size: 0.9rem;"><?= $prenda['nombre'] ?></h6>

                                    <p class="text-dark small mb-1 fw-bold text-uppercase" style="letter-spacing: 1px;">
                                        Talla: <?= $prenda['talla'] ?? 'N/A' ?>
                                    </p>

                                    <p class="fw-bold fs-5 mb-2"><?= number_format($prenda['precio'], 2) ?> €</p>
                                </div>
                            </a>
                            <div class="d-flex align-items-center justify-content-between gap-1 mt-auto px-1 pt-2">

                                <form action="controllers/carritoController.php" method="POST" class="flex-grow-1 m-0">
                                    <input type="hidden" name="accion" value="agregar">
                                    <input type="hidden" name="idPrenda" value="<?= $prenda['id'] ?>">
                                    <input type="hidden" name="color_id" value="<?= $prenda['color_id'] ?>">
                                    <input type="hidden" name="talla" value="<?= $prenda['talla'] ?>">
                                    <input type="hidden" name="cantidad" value="1">
                                    <input type="hidden" name="origen" value="segundaMano">

                                    <button type="submit" class="btn btn-principal rounded-0 w-100 text-uppercase fw-bold py-1 px-0"
                                        style="height: 40px; font-size: 0.75rem; letter-spacing: 1px;">
                                        Añadir
                                    </button>
                                </form>
                                <button type="button" class="btn btn-outline-dark btn-toggle-favorito btn-favorito-custom btn-favorito-std d-flex justify-content-center align-items-center rounded-0 m-0"
                                    style="height: 40px; width: 40px; padding: 0;"
                                    data-id="<?= $prenda['id'] ?>"
                                    data-color="<?= $prenda['color_id'] ?>">
                                    <i class="bi bi-heart"></i>
                                </button>

                            </div>

                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>


</main>

<?php
include './includes/footer.php';
?>