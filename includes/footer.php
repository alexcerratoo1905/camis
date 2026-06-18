<footer class="bg-black text-white pt-5 pb-4 mt-5">
    <div class="container text-center text-md-start">
        <div class="row text-center text-md-start">
            
            <div class="col-md-3 col-lg-3 col-xl-3 mx-auto mb-4 mt-3">
                <h5 class="text-uppercase mb-4 fw-bold text-white">Herror</h5>
                <p class="text-muted">Streetwear diseñado en España para el mundo. Calidad premium.</p>
            </div>

            <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mb-4 mt-3">
                <h6 class="text-uppercase mb-4 fw-bold text-white">Ayuda</h6>
                <p class="mb-2"><a href="#" class="text-muted text-decoration-none hover-white">Envíos</a></p>
                <p class="mb-2"><a href="#" class="text-muted text-decoration-none hover-white">Devoluciones</a></p>
            </div>

            <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-4 mt-3">
                <h6 class="text-uppercase mb-4 fw-bold text-white">Contacto</h6>
                <p class="text-muted"><i class="bi bi-envelope me-3"></i> herror@gmail.com</p>
            </div>
        </div>
        
        <hr class="mb-4 border-secondary">
        
        <div class="row align-items-center">
            <div class="col-12 text-center text-md-start">
                <p class="text-muted small mb-0">© 2026 Copyright: <strong class="text-white">Pablo TFG</strong></p>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const mensajeAlerta = "<?php echo isset($_GET['mensaje']) ? $_GET['mensaje'] : ''; ?>";
    const errorAlerta = "<?php echo isset($_GET['error']) ? $_GET['error'] : ''; ?>";
    const bienvenidoAlerta = "<?php echo isset($_GET['bienvenido']) ? $_GET['bienvenido'] : ''; ?>";
    const nombreUsuario = "<?php echo isset($_SESSION['nombre']) ? $_SESSION['nombre'] : ''; ?>";
    const sesionCerradaAlerta = "<?php echo isset($_GET['sesionCerrada']) ? $_GET['sesionCerrada'] : ''; ?>";
</script>

<script src="public/js/global.js?v=<?php echo time(); ?>"></script>



</body>
</html>