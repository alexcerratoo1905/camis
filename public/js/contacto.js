document.addEventListener("DOMContentLoaded", function () {
    const formContacto = document.getElementById('formContacto');
    
    const swalContacto = Swal.mixin({
        confirmButtonColor: 'var(--color-principal, #000)',
        showClass: { popup: 'animate__animated animate__fadeIn animate__faster' },
        hideClass: { popup: 'animate__animated animate__fadeOut animate__faster' },
        borderRadius: '0'
    });
    
    if (formContacto) {
        formContacto.addEventListener('submit', function (e) {
            e.preventDefault();
            
            const btn = document.getElementById('btnEnviarContacto');
            const textoOriginal = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> ENVIANDO...';

            const datos = {
                nombre: document.getElementById('nombreContacto').value,
                email: document.getElementById('emailContacto').value,
                mensaje: document.getElementById('mensajeContacto').value
            };

            fetch('controllers/apiContactoController.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datos)
            })
            .then(response => response.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = textoOriginal;
                
                if (data.exito) {
                    swalContacto.fire({
                        icon: 'success',
                        title: '¡Mensaje Enviado!',
                        text: 'Hemos recibido tu consulta. Nos pondremos en contacto contigo pronto.'
                    });
                    formContacto.reset();
                } else {
                    throw new Error("Error en el servidor");
                }
            })
            .catch(error => {
                btn.disabled = false;
                btn.innerHTML = textoOriginal;
                swalContacto.fire({
                    icon: 'error',
                    title: 'Ups...',
                    text: 'Hubo un problema al enviar el mensaje. Inténtalo de nuevo.'
                });
            });
        });
    }
});