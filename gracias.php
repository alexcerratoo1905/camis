<?php
require_once 'includes/auth.php';
include './includes/header.php';
?>

<main id="graciasCompra" class="container my-5 py-5 mt-5 d-flex justify-content-center align-items-center" style="min-height: 60vh;" >
    <div class="card border-dark border-3 rounded-0 shadow-lg text-center p-5" style="max-width: 600px;">
        <div class="card-body">
            
            <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
            
            <h1 class="display-5 fw-bold text-uppercase mt-4 mb-3">¡Pago Completado!</h1>
            <p class="fs-5 text-muted mb-5">Hemos recibido tu pago correctamente. Estamos preparando tu pedido con todo el cuidado que se merece.</p>
            
            <div class="d-flex justify-content-center align-items-center mb-3">
                <div class="spinner-border text-dark" role="status" style="width: 3rem; height: 3rem; border-width: 0.25em;">
                    <span class="visually-hidden">Procesando...</span>
                </div>
            </div>
            
            <p class="small text-uppercase fw-bold text-muted mt-3">Redirigiendo a tu perfil...</p>
            
        </div>
    </div>
</main>



<?php include './includes/footer.php'; ?>