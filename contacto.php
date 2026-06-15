<?php 
session_start();
include './includes/header.php'; 
?>

<main class="container my-5 py-5 mt-5" style="min-height: 70vh; display: flex; align-items: center;">
    <div class="row w-100 justify-content-center m-0">
        <div class="col-12 col-md-8 col-lg-6">
            
            <div class="text-center mb-4">
                <i class="bi bi-chat-square-text display-4 text-dark mb-3 d-block"></i>
                <h2 class="fw-bold text-uppercase" style="letter-spacing: 2px;">Contacto</h2>
                <p class="text-muted mt-3">¿Dudas sobre tallas, pedidos o nuestras colecciones? Escríbenos y nuestro equipo te responderá lo antes posible.</p>
            </div>
            
            <div class="card border-0 shadow-sm rounded-0 p-4 p-md-5 bg-white">
                <form id="formContacto">
                    
                    <div class="mb-4">
                        <label for="nombreContacto" class="form-label small fw-bold text-uppercase text-muted">Nombre</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 rounded-0"><i class="bi bi-person text-muted"></i></span>
                            <input type="text" id="nombreContacto" class="form-control rounded-0 border-start-0 py-2" placeholder="Tu nombre" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="emailContacto" class="form-label small fw-bold text-uppercase text-muted">Correo Electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 rounded-0"><i class="bi bi-envelope text-muted"></i></span>
                            <input type="email" id="emailContacto" class="form-control rounded-0 border-start-0 py-2" placeholder="tu@email.com" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="mensajeContacto" class="form-label small fw-bold text-uppercase text-muted">Mensaje</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 rounded-0"><i class="bi bi-pencil text-muted"></i></span>
                            <textarea id="mensajeContacto" class="form-control rounded-0 border-start-0 py-2" rows="4" placeholder="¿En qué podemos ayudarte?" required></textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-principal w-100 rounded-0 py-3 text-uppercase fw-bold ls-1 mt-2" id="btnEnviarContacto">
                        Enviar Mensaje
                    </button>

                </form>
            </div>
        </div>
    </div>
</main>

<script src="public/js/contacto.js"></script>

<?php include './includes/footer.php'; ?>