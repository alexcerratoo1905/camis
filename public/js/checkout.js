document.addEventListener('DOMContentLoaded', function() {
    
    const radiosPago = document.querySelectorAll('input[name="metodo_pago"]');
    if (radiosPago.length > 0) {
        radiosPago.forEach(radio => {
            radio.addEventListener('change', cambiarMetodoPago);
        });
        cambiarMetodoPago(); 
    }

    const visualNumero = document.getElementById('tarjeta-numero-visual');
    const visualNombre = document.getElementById('tarjeta-nombre-visual');
    const visualMes = document.getElementById('tarjeta-mes-visual');
    const visualAnio = document.getElementById('tarjeta-anio-visual');
    const visualCvv = document.getElementById('tarjeta-cvv-visual');
    const tarjetaAnimada = document.getElementById('tarjeta-credito');

    const inputNumero = document.getElementById('input-numero');
    const inputNombre = document.getElementById('input-nombre');
    const inputMes = document.getElementById('input-mes');
    const inputAnio = document.getElementById('input-anio');
    const inputCvv = document.getElementById('input-cvv');

    if(inputNumero) { 
        
        inputNumero.addEventListener('input', function(e) {
            let valor = e.target.value.replace(/\D/g, ''); 
            let formateado = valor.match(/.{1,4}/g)?.join(' ') || ''; 
            e.target.value = formateado;
            
            visualNumero.textContent = formateado !== '' ? formateado : '#### #### #### ####';
        });

        inputNombre.addEventListener('input', function(e) {
            let nombre = e.target.value.toUpperCase();
            visualNombre.textContent = nombre !== '' ? nombre : 'NOMBRE APELLIDOS';
        });

        inputMes.addEventListener('change', function(e) {
            visualMes.textContent = e.target.value;
        });

        inputAnio.addEventListener('change', function(e) {
            visualAnio.textContent = e.target.value;
        });

        inputCvv.addEventListener('input', function(e) {
            let valor = e.target.value.replace(/\D/g, ''); 
            e.target.value = valor;
            visualCvv.textContent = valor;
        });

        inputCvv.addEventListener('focus', function() {
            tarjetaAnimada.classList.add('girada');
        });

        inputCvv.addEventListener('blur', function() {
            tarjetaAnimada.classList.remove('girada');
        });
    }
});

function cambiarMetodoPago() {
    const radioTarjeta = document.getElementById('pago_tarjeta');
    const cajaTarjeta = document.getElementById('caja_tarjeta');
    const cajaBizum = document.getElementById('caja_bizum');
    const formTarjeta = document.getElementById('form_tarjeta');
    const formBizum = document.getElementById('form_bizum');

    if (!cajaTarjeta || !cajaBizum || !formTarjeta || !formBizum) return;

    if (radioTarjeta.checked) {
        cajaTarjeta.classList.replace('border-secondary', 'border-dark');
        cajaTarjeta.classList.replace('border-1', 'border-2');
        cajaTarjeta.classList.replace('bg-light', 'bg-white');
        cajaTarjeta.style.opacity = '1';
        formTarjeta.style.display = 'block';

        cajaBizum.classList.replace('border-dark', 'border-secondary');
        cajaBizum.classList.replace('border-2', 'border-1');
        cajaBizum.classList.replace('bg-white', 'bg-light');
        cajaBizum.style.opacity = '0.7';
        formBizum.style.display = 'none';
    } else {
        cajaBizum.classList.replace('border-secondary', 'border-dark');
        cajaBizum.classList.replace('border-1', 'border-2');
        cajaBizum.classList.replace('bg-light', 'bg-white');
        cajaBizum.style.opacity = '1';
        formBizum.style.display = 'block';

        cajaTarjeta.classList.replace('border-dark', 'border-secondary');
        cajaTarjeta.classList.replace('border-2', 'border-1');
        cajaTarjeta.classList.replace('bg-white', 'bg-light');
        cajaTarjeta.style.opacity = '0.7';
        formTarjeta.style.display = 'none';
    }
}

    const formPago = document.getElementById('formPago');
    if (formPago) {
        formPago.addEventListener('submit', function(e) {
            const radioTarjeta = document.getElementById('pago_tarjeta');
            
            if (radioTarjeta && radioTarjeta.checked) {
                const num = document.getElementById('input-numero').value;
                const nombre = document.getElementById('input-nombre').value;
                const mes = document.getElementById('input-mes').value;
                const anio = document.getElementById('input-anio').value;
                const cvv = document.getElementById('input-cvv').value;

                if (num.length < 19 || nombre === "" || mes === "MM" || anio === "AA" || cvv.length < 3) {
                    e.preventDefault(); 
                    
                    Swal.fire({
                        icon: 'warning',
                        title: 'Datos Incompletos',
                        text: 'Por favor, rellena todos los datos de la tarjeta correctamente antes de pagar.',
                        confirmButtonColor: '#000'
                    });
                }
            }
        });
    }