<?php
require_once 'includes/auth.php';
include './includes/header.php';
?>

<main class="container my-5 py-5 text-center d-flex flex-column justify-content-center align-items-center" style="min-height: 60vh;">
    <i class="bi bi-calendar-check text-success display-1 mb-4"></i>
    <h1 class="fw-bold text-uppercase" style="letter-spacing: 2px;">¡Reserva Confirmada!</h1>
    <p class="text-muted fs-5">Estamos gestionando tu cita para nuestra cita.</p>
    
    <div class="spinner-border text-dark mt-4" role="status" style="width: 3rem; height: 3rem;">
        <span class="visually-hidden">Cargando...</span>
    </div>
    
    <p class="small text-muted mt-3 text-uppercase fw-bold">Redirigiendo a tu perfil...</p>
</main>

<script>
    setTimeout(function() {
        window.location.href = 'perfil.php?seccion=citas';
    }, 3000);
</script>

<?php include './includes/footer.php'; ?>