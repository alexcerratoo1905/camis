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

<div id="herror-chatbot" class="chatbot-wrapper">
    
    <div id="chatbot-window" class="chatbot-window d-none shadow-lg">
        
        <div class="chatbot-header">
            <div class="d-flex align-items-center">
                <strong>ASISTENTE HERROR</strong>
            </div>
            <button id="chatbot-close" class="btn-close btn-close-white shadow-none" aria-label="Close"></button>
        </div>
        
        <div id="chatbot-messages" class="chatbot-messages">
            <div class="chat-msg bot-msg shadow-sm">
                ¡Hola! Soy el asistente inteligente de HERROR.<br>¿Tienes dudas sobre tallas, reservas en tienda física o nuestras colecciones?
            </div>
        </div>
        
        <form id="chatbot-form" class="chatbot-input-area">
            <input type="text" id="chatbot-input" class="shadow-none" placeholder="Escribe tu pregunta..." autocomplete="off" required>
            <button type="submit" id="chatbot-send-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-send-fill" viewBox="0 0 16 16">
                  <path d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855H.766l-.452.18a.5.5 0 0 0-.082.887l.41.26.001.002 4.995 3.178 3.178 4.995.002.002.26.41a.5.5 0 0 0 .886-.083l6-15Zm-1.833 1.89L6.637 10.07l-.215-.338a.5.5 0 0 0-.154-.154l-.338-.215 7.494-7.494 1.178-.471-.47 1.178Z"/>
                </svg>
            </button>
        </form>
    </div>

    <button id="chatbot-toggle" class="chatbot-toggle-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-chat-dots-fill" viewBox="0 0 16 16">
            <path d="M16 8c0 3.866-3.582 7-8 7a9 9 0 0 1-2.347-.306c-2.013 3.789-5.212 2.15-5.212 2.15a.5.5 0 0 1 .115-.585 5.4 5.4 0 0 0 1.398-1.55A7.7 7.7 0 0 1 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7M5 8a1 1 0 1 0-2 0 1 1 0 0 0 2 0m4 0a1 1 0 1 0-2 0 1 1 0 0 0 2 0m3 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/>
        </svg>
    </button>
</div>

</body>
</html>