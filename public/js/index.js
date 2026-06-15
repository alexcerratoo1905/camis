const fechaLanzamiento = new Date("2026-06-20T19:00:00").getTime();

const temporizador = setInterval(() => {

    const fechaActual = new Date().getTime();
    const tiempoRestante = fechaLanzamiento - fechaActual;
    const dias = Math.floor(tiempoRestante / (1000 * 60 * 60 * 24));
    const horas = Math.floor((tiempoRestante % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutos = Math.floor((tiempoRestante % (1000 * 60 * 60)) / (1000 * 60));
    const segundos = Math.floor((tiempoRestante % (1000 * 60)) / 1000);

    document.getElementById("dias").innerHTML = dias;
    document.getElementById("horas").innerHTML = horas;
    document.getElementById("minutos").innerHTML = minutos;
    document.getElementById("segundos").innerHTML = segundos;



}, 1000);

const formSolicitar = document.getElementById('formSolicitarAcceso');
if(formSolicitar){
    formSolicitar.addEventListener('submit', function(e){
        e.preventDefault();
        const emailInput = document.getElementById('emailAcceso');
        const email = emailInput.value;
        const btn = e.target.querySelector('button');
        
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> ENVIANDO...';

        fetch('controllers/solicitarCodigoController.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email: email })
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerText = "Enviar solicitud";

            if(data.status === 'success'){
                Swal.fire({
                    icon: 'success',
                    title: '¡Pase solicitado!',
                    text: 'En breves momentos recibirás un código de un solo uso en tu correo electrónico.',
                    confirmButtonColor: 'var(--color-principal, #000)'
                });
                emailInput.value = ""; 
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalSolicitarAcceso'));
                modal.hide();
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            btn.disabled = false;
            btn.innerText = "Enviar solicitud";
            console.error("Error:", error);
            Swal.fire({
                icon: 'error',
                title: 'No se pudo enviar',
                text: 'Hubo un problema técnico: ' + error.message,
                confirmButtonColor: 'var(--color-principal, #000)'
            });
        });
    });
}