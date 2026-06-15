<?php
require_once 'includes/auth.php';
include './includes/header.php';
?>

<div class="container-fluid p-0 position-relative" style="height: 40vh; margin-top: 80px;">
    <img src="public/img/fondo.jpg" class="w-100 h-100 object-fit-cover" alt="Showroom HERROR" style="filter: brightness(0.6);">
<div class="position-absolute top-50 start-50 translate-middle text-center text-white w-100 px-3">
        <h1 class="display-4 fw-bold text-uppercase titulo-citas">Showroom Exclusivo</h1>
        <p class="fs-5">Reserva tu cita privada en nuestra tienda</p>
    </div>
</div>

<main class="container my-5 py-4">
    <div class="row g-5">

<div class="col-lg-5 d-flex flex-column">
            <h2 class="fw-bold text-uppercase mb-4">La Experiencia HERROR</h2>
            <p class="text-muted mb-4">
                Queremos ofrecerte una atención completamente personalizada. Reserva tu cita para descubrir nuestra nueva colección, 
                recibir asesoramiento de estilo exclusivo o recoger tus pedidos online en un entorno único.
            </p>
            
            <div class="flex-grow-1 bg-light position-relative mapa-citas">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3036.8930759449527!2d-3.7068409225622716!3d40.433366471436706!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd422889dc61cd51%3A0x7d87718fe6a0552f!2sCalle%20de%20Fuencarral%2C%20Madrid!5e0!3m2!1ses!2ses!4v1776428358937!5m2!1ses!2ses" 
                    class="position-absolute top-0 start-0 w-100 h-100 border-0" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>


            <div class="bg-light p-4 text-center mt-5">
                <i class="bi bi-shield-check display-4 mb-3 d-block text-dark"></i>
                <p class="small text-muted mb-0 fw-bold text-uppercase">Aforo limitado a 10 personas por franja horaria para garantizar la máxima exclusividad y atención.</p>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-0 p-4 p-md-5">
                <h3 class="fw-bold text-uppercase mb-4">Reserva tu visita</h3>

                <form id="form-cita" action="controllers/citasController.php" method="POST">

                    <div class="mb-4">
                        <label class="form-label fw-bold text-uppercase small text-muted">¿Cuál es el motivo de tu visita?</label>
                        <select name="motivo" class="form-select rounded-0 py-2" required>
                            <option value="" disabled selected>Selecciona un motivo...</option>
                            <option value="Probar Nueva Colección">Probar Nueva Colección</option>
                            <option value="Asesoramiento de Estilo">Asesoramiento de Estilo</option>
                            <option value="Recogida de Pedido Online">Recogida de Pedido Online</option>
                            <option value="Cambios y Devoluciones">Cambios y Devoluciones</option>
                        </select>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label fw-bold text-uppercase small text-muted">Fecha de la cita</label>
                            <input type="date" id="fecha-cita" name="fecha" class="form-control rounded-0 py-2" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-uppercase small text-muted">Hora</label>
                            <select id="hora-cita" name="hora" class="form-select rounded-0 py-2" required disabled>
                                <option value="" disabled selected>Primero selecciona una fecha</option>
                                <option value="10:00">10:00 - 11:00</option>
                                <option value="11:00">11:00 - 12:00</option>
                                <option value="12:00">12:00 - 13:00</option>
                                <option value="13:00">13:00 - 14:00</option>
                                <option value="17:00">17:00 - 18:00</option>
                                <option value="18:00">18:00 - 19:00</option>
                                <option value="19:00">19:00 - 20:00</option>
                            </select>
                            <div id="hora-help" class="form-text small text-danger d-none">Algunas horas están completas.</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-uppercase small text-muted">Comentarios adicionales (Opcional)</label>
                        <textarea name="comentarios" class="form-control rounded-0" rows="3" placeholder="Ej: Me gustaría probarme la bomber verde en talla M..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-dark w-100 rounded-0 py-3 text-uppercase fw-bold" style="letter-spacing: 2px;">
                        Confirmar Reserva
                    </button>
                </form>
            </div>
        </div>

    </div>
</main>

<script>
    document.getElementById('fecha-cita').addEventListener('change', function() {
        let fechaSeleccionada = this.value;
        let selectHora = document.getElementById('hora-cita');
        let horaHelp = document.getElementById('hora-help');

        if (fechaSeleccionada) {
            selectHora.disabled = false;

            Array.from(selectHora.options).forEach(opt => opt.disabled = false);
            horaHelp.classList.add('d-none');

            fetch('controllers/apiCitasDisponibles.php?fecha=' + fechaSeleccionada)
                .then(response => response.json())
                .then(horasLlenas => {
                    if (horasLlenas.length > 0) {
                        horaHelp.classList.remove('d-none'); 

                        Array.from(selectHora.options).forEach(opt => {
                            if (horasLlenas.includes(opt.value)) {
                                opt.disabled = true;
                                opt.text = opt.value + " (Aforo Completo)";
                            } else if (opt.value !== "") {
                                opt.text = opt.value + " - " + (parseInt(opt.value) + 1) + ":00"; 
                            }
                        });
                    }
                })
                .catch(error => console.error("Error comprobando aforo:", error));
        }
    });
</script>

<?php include './includes/footer.php'; ?>