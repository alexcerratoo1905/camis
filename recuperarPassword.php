<?php

include './includes/header.php';

?>

<main class="container my-5 py-5 mt-5" style="min-height: 65vh; display: flex; align-items: center;">
    <div class="row w-100 justify-content-center m-0">
        <div class="col-12 col-md-8 col-lg-5">
            
            <div class="card border-0 shadow-sm rounded-0 p-5 bg-white">
                
                <div class="text-center mb-4">
                    <i class="bi bi-key display-4 text-dark mb-3 d-block"></i>
                    <h2 class="fw-bold text-uppercase" style="letter-spacing: 2px;">Recuperar Contraseña</h2>
                    <p class="text-muted mt-3">Introduce el correo electrónico asociado a tu cuenta y te enviaremos las instrucciones para restablecerla.</p>
                </div>

                <?php if (isset($_GET["error"]) && $_GET["error"] == "emailNoExiste"): ?>
                    <div class="alert alert-danger rounded-0 small fw-bold text-center">
                        <i class="bi bi-exclamation-triangle me-1"></i> No encontramos ninguna cuenta con ese correo.
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET["mensaje"]) && $_GET["mensaje"] == "correoEnviado"): ?>
                    <div class="alert alert-success rounded-0 small fw-bold text-center">
                        <i class="bi bi-envelope-check me-1"></i> Hemos enviado un enlace de recuperación a tu correo, mira por si acaso la bandeja de spam.
                    </div>
                <?php endif; ?>

                <form action="controllers/recuperarPasswordController.php" method="POST">
                    
                    <input type="hidden" name="accion" value="recuperarPassword">

                    <div class="mb-4">
                        <label for="email" class="form-label small fw-bold text-uppercase text-muted">Correo Electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 rounded-0"><i class="bi bi-envelope text-muted"></i></span>
                            <input type="email" class="form-control rounded-0 border-start-0 py-2" id="email" name="email" placeholder="tu@email.com" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-dark rounded-0 w-100 py-3 text-uppercase fw-bold ls-1 mb-3">
                        Enviar Enlace
                    </button>

                    <div class="text-center mt-3">
                        <a href="index.php" class="text-muted text-decoration-none small fw-bold">
                            <i class="bi bi-arrow-left me-1"></i> Volver al inicio
                        </a>
                    </div>

                </form>
            </div>

        </div>
    </div>
</main>

<?php 

include './includes/footer.php'; 

?>