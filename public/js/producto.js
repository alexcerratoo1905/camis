const mainProducto = document.getElementById("mainProducto");




function cambiarFoto(elementoClicado, urlNuevaFoto) {
    let imgPrincipal = document.getElementById('imagenPrincipal');

    imgPrincipal.classList.add('oculto-transicion');

    setTimeout(function () {
        imgPrincipal.src = urlNuevaFoto;
        imgPrincipal.classList.remove('oculto-transicion');
    }, 150);

    let miniaturas = document.querySelectorAll('.miniatura-galeria');
    miniaturas.forEach(function (miniatura) {
        miniatura.classList.remove('borde-activo');
    });

    elementoClicado.classList.add('borde-activo');
}

function cambiarConFlechas(direccion) {
    let todasLasMiniaturas = Array.from(document.querySelectorAll('.miniatura-galeria'));

    let miniaturasVisibles = todasLasMiniaturas.filter(min => min.style.display !== 'none');

    if (miniaturasVisibles.length === 0) return;

    let indexActual = miniaturasVisibles.findIndex(min => min.classList.contains('borde-activo'));
    let nuevoIndex;

    if (direccion === 'next') {
        nuevoIndex = indexActual + 1;
        if (nuevoIndex >= miniaturasVisibles.length) {
            nuevoIndex = 0;
        }
    } else {
        nuevoIndex = indexActual - 1;
        if (nuevoIndex < 0) {
            nuevoIndex = miniaturasVisibles.length - 1;
        }
    }

    miniaturasVisibles[nuevoIndex].click();
}


function seleccionarColor(colorId, elementoClicado) {



    let envolturas = document.querySelectorAll('.color-swatch-wrapper');
    envolturas.forEach(function (env) {
        env.classList.remove('border-dark', 'p-1');
        env.classList.add('border-light');
    });
    elementoClicado.classList.remove('border-light');
    elementoClicado.classList.add('border-dark', 'p-1');

    let miniaturas = document.querySelectorAll('.miniatura-color');
    let primeraVisible = null;

    miniaturas.forEach(function (miniatura) {
        if (miniatura.getAttribute('data-color-id') == colorId) {
            miniatura.style.display = 'block';
            if (!primeraVisible) {
                primeraVisible = miniatura;
            }
        } else {
            miniatura.style.display = 'none';
        }
    });

    if (primeraVisible) {
        primeraVisible.click();
    }

    const selectTalla = document.getElementById('talla');
    if (selectTalla && typeof tallasProducto !== 'undefined') {

        selectTalla.innerHTML = '<option value="" selected disabled>Selecciona tu talla</option>';

        const tallasDelColor = tallasProducto.filter(t => t.color_id == colorId);

        tallasDelColor.forEach(tallaObj => {
            let opcion = document.createElement('option');
            opcion.value = tallaObj.talla;

            if (tallaObj.stock == 0) {
                opcion.disabled = true;
                opcion.textContent = tallaObj.talla + ' (Agotado)';
            } else if (tallaObj.stock <= 10) {
                opcion.textContent = tallaObj.talla + ' - ¡Rápido, quedan ' + tallaObj.stock + '!';
            } else {
                opcion.textContent = tallaObj.talla;
            }

            selectTalla.appendChild(opcion);
        });
    }

    let inputColor = document.getElementById('input_color_id');
    if (inputColor) {
        inputColor.value = colorId;
    }

    let btnFav = document.getElementById('btn-favorito-ficha');

    if (btnFav) {
        btnFav.setAttribute('data-color', colorId);

        let idPrenda = btnFav.getAttribute('data-id');
        let comboActual = idPrenda + '-' + colorId; 
        let iconoCorazon = btnFav.querySelector('i');

        if (typeof listaFavoritosJS !== 'undefined') {
            if (listaFavoritosJS.includes(comboActual)) {
                iconoCorazon.classList.remove('bi-heart');
                iconoCorazon.classList.add('bi-heart-fill');
            } else {
                iconoCorazon.classList.remove('bi-heart-fill');
                iconoCorazon.classList.add('bi-heart');
            }
        }
    }



    if (mainProducto) {
        mainProducto.dataset.colorPrenda = colorId;

        if (primeraVisible) {
            mainProducto.dataset.imagen = primeraVisible.src;
        }

        if (typeof guardarPrendasRecientes === "function") {
            guardarPrendasRecientes();
        }
    }

    let idPrendaBase = mainProducto.dataset.id;
    let botonOffcanvas = document.getElementById('btnCompletarLook');
    let contenedorPrendasLook = document.getElementById('contenedorPrendasLook');

    if (botonOffcanvas && contenedorPrendasLook) {

        botonOffcanvas.classList.add('d-none');

        fetch(`controllers/apiLookController.php?idPrenda=${idPrendaBase}&idColor=${colorId}`)
            .then(res => res.json())
            .then(datos => {
                if (datos.exito && datos.productos.length > 0) {

                    botonOffcanvas.classList.remove('d-none');

                    contenedorPrendasLook.innerHTML = '';

                    datos.productos.forEach(prendaLook => {
                        let iconoCorazonLook = 'bi-heart';
                        if (typeof listaFavoritosJS !== 'undefined' && listaFavoritosJS.includes(prendaLook.id + '-' + prendaLook.color_id)) {
                            iconoCorazonLook = 'bi-heart-fill';
                        }

                        let htmlPrenda = `
                            <div class="col-6 position-relative d-flex flex-column mb-4">
                                <div class="card product-card border-0 bg-transparent position-relative">
                                    <div class="img-wrapper position-relative overflow-hidden">
                                        <a href="fichaProducto.php?idPrenda=${prendaLook.id}&color=${prendaLook.color_id}" class="text-decoration-none text-dark d-block">
                                            <img src="${prendaLook.url_imagen}" class="card-img-top rounded-0" alt="${prendaLook.nombre}" style="height: 250px; object-fit: cover;">
                                        </a>
                                        <div id="overlay-tallas-${prendaLook.id}" class="overlay-tallas d-none position-absolute bottom-0 start-0 w-100 bg-white bg-opacity-75 p-2 text-center">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="small fw-bold text-uppercase" style="letter-spacing: 1px; font-size: 0.7rem;">Talla</span>
                                                <button type="button" class="btn-close" style="font-size: 0.6rem;" onclick="cerrarOverlayTallas(event, ${prendaLook.id})"></button>
                                            </div>
                                            <div id="contenedor-botones-${prendaLook.id}" class="d-flex justify-content-center flex-wrap gap-1"></div>
                                        </div>
                                    </div>
                                    <div class="card-body text-center px-0 pb-1 mt-2">
                                        <a href="fichaProducto.php?idPrenda=${prendaLook.id}&color=${prendaLook.color_id}" class="text-decoration-none text-dark d-block">
                                            <h6 class="card-title text-uppercase fw-bold mb-1 text-truncate" style="font-size: 0.8rem;">${prendaLook.nombre}</h6>
                                            <p class="card-text mb-0 small">${prendaLook.precio} €</p>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between gap-1 mt-2 px-1">
                                    <button type="button" class="btn btn-principal rounded-0 flex-grow-1 text-uppercase fw-bold py-1 px-0" style="font-size: 0.7rem;" onclick="abrirOverlayTallas(event, ${prendaLook.id}, ${prendaLook.color_id})">Añadir</button>
                                    <button type="button" class="btn btn-toggle-favorito d-flex justify-content-center align-items-center rounded-0 p-1 btn-favorito-custom btn-favorito-sm" data-id="${prendaLook.id}" data-color="${prendaLook.color_id}">
                                        <i class="bi ${iconoCorazonLook}" style="font-size: 0.9rem;"></i>
                                    </button>
                                </div>
                            </div>
                        `;
                        contenedorPrendasLook.innerHTML += htmlPrenda;
                    });
                }
            })
            .catch(error => {
                console.error('La comunicación falló, pero el botón se queda oculto:', error);
            });
    }

}

document.addEventListener("DOMContentLoaded", function () {


    const urlParams = new URLSearchParams(window.location.search);
    const colorIdUrl = urlParams.get('color');

    let colorInicial = null;

    if (colorIdUrl) {
        colorInicial = document.querySelector('.color-swatch-wrapper[data-color-id="' + colorIdUrl + '"]');
    }

    if (!colorInicial) {
        colorInicial = document.querySelector('.color-swatch-wrapper');
    }

    if (colorInicial) {
        seleccionarColor(colorInicial.getAttribute('data-color-id'), colorInicial);
    }
});

if (typeof gsap !== 'undefined' && typeof MorphSVGPlugin !== 'undefined') {
    gsap.registerPlugin(MorphSVGPlugin);

    document.querySelectorAll('.add-to-cart').forEach(button => {
        let morph = button.querySelector('.morph path'),
            shirt = button.querySelectorAll('.shirt svg > path');

        button.addEventListener('pointerdown', e => {
            if (button.classList.contains('active')) { return; }
            gsap.to(button, { '--background-scale': .97, duration: .15 });
        });

        button.addEventListener('click', e => {
            e.preventDefault();

            let selectTalla = button.closest('form').querySelector('#talla');
            if (selectTalla && (!selectTalla.value || selectTalla.value === "")) {
                Swal.fire({
                    icon: 'warning',
                    title: '¡Falta la talla!',
                    text: 'Por favor, selecciona una talla antes de añadir al carrito.',
                    confirmButtonColor: 'var(--color-principal, #000)'
                });
                return; 
            }

            if (button.classList.contains('active')) { return; }
            button.classList.add('active');

            gsap.to(button, {
                keyframes: [{
                    '--background-scale': .97, duration: .15
                }, {
                    '--background-scale': 1, delay: .125, duration: 1.2, ease: 'elastic.out(1, .6)'
                }]
            });

            gsap.to(button, {
                keyframes: [{
                    '--shirt-scale': 1, '--shirt-y': '-42px', '--cart-x': '0px', '--cart-scale': 1, duration: .4, ease: 'power1.in'
                }, {
                    '--shirt-y': '-40px', duration: .3
                }, {
                    '--shirt-y': '16px', '--shirt-scale': .9, duration: .25, ease: 'none'
                }, {
                    '--shirt-scale': 0, duration: .3, ease: 'none'
                }]
            });

            gsap.to(button, { '--shirt-second-y': '0px', delay: .835, duration: .12 });

            gsap.to(button, {
                keyframes: [{
                    '--cart-clip': '12px', '--cart-clip-x': '3px', delay: .9, duration: .06
                }, {
                    '--cart-y': '2px', duration: .1
                }, {
                    '--cart-tick-offset': '0px', '--cart-y': '0px', duration: .2, onComplete() { button.style.overflow = 'hidden'; }
                }, {
                    '--cart-x': '52px', '--cart-rotate': '-15deg', duration: .2
                }, {
                    '--cart-x': '200px', '--cart-rotate': '0deg', duration: .3, clearProps: true, onComplete() {
                        button.style.overflow = 'hidden';
                        button.style.setProperty('--text-o', 0);
                        button.style.setProperty('--text-x', '0px');
                        button.style.setProperty('--cart-x', '-160px');
                    }
                }, {
                    '--text-o': 1, '--text-x': '20px', '--cart-x': '-100px', '--cart-scale': .75, duration: .25, clearProps: true, onComplete() {
                        button.classList.remove('active');
                    }
                }]
            });

            gsap.to(button, { keyframes: [{ '--text-o': 0, duration: .3 }] });

            gsap.to(morph, {
                keyframes: [{
                    morphSVG: 'M0 12C6 12 20 10 32 0C43.9024 9.99999 58 12 64 12V13H0V12Z', duration: .25, ease: 'power1.out'
                }, {
                    morphSVG: 'M0 12C6 12 17 12 32 12C47.9024 12 58 12 64 12V13H0V12Z', duration: .15, ease: 'none'
                }]
            });

            gsap.to(shirt, {
                keyframes: [{
                    morphSVG: 'M4.99997 3L8.99997 1.5C8.99997 1.5 10.6901 3 12 3C13.3098 3 15 1.5 15 1.5L19 3L23.5 8L20.5 11L19 9.5L18 22.5C18 22.5 14 21.5 12 21.5C10 21.5 5.99997 22.5 5.99997 22.5L4.99997 9.5L3.5 11L0.5 8L4.99997 3Z', duration: .25, delay: .25
                }, {
                    morphSVG: 'M4.99997 3L8.99997 1.5C8.99997 1.5 10.6901 3 12 3C13.3098 3 15 1.5 15 1.5L19 3L23.5 8L20.5 11L19 9.5L18.5 22.5C18.5 22.5 13.5 22.5 12 22.5C10.5 22.5 5.5 22.5 5.5 22.5L4.99997 9.5L3.5 11L0.5 8L4.99997 3Z', duration: .85, ease: 'elastic.out(1, .5)'
                }, {
                    morphSVG: 'M4.99997 3L8.99997 1.5C8.99997 1.5 10.6901 3 12 3C13.3098 3 15 1.5 15 1.5L19 3L22.5 8L19.5 10.5L19 9.5L17.1781 18.6093C17.062 19.1901 16.778 19.7249 16.3351 20.1181C15.4265 20.925 13.7133 22.3147 12 23C10.2868 22.3147 8.57355 20.925 7.66487 20.1181C7.22198 19.7249 6.93798 19.1901 6.82183 18.6093L4.99997 9.5L4.5 10.5L1.5 8L4.99997 3Z', duration: 0, delay: 1.25
                }]
            });

            setTimeout(() => {
                button.closest('form').submit();
            }, 1900);
        });
    });
}




function guardarPrendasRecientes() {


    if (!mainProducto) return;


    let prendaActual = {
        id: mainProducto.dataset.id,
        nombre: mainProducto.dataset.nombre,
        precio: mainProducto.dataset.precio,
        imagen: mainProducto.dataset.imagen,
        colorPrenda: mainProducto.dataset.colorPrenda,
        rebaja: mainProducto.dataset.rebaja

    }



    console.log("Prenda capturada: ", prendaActual);

    let aPrendasRecientes = [];
    let datosLocal = localStorage.getItem('prendasRecientes');

    try {
        if (datosLocal) {
            aPrendasRecientes = JSON.parse(datosLocal);
        }

    } catch (error) {
        aPrendasRecientes = [];
    }

    aPrendasRecientes = aPrendasRecientes.filter(function (prendaGuardada) {
        let prendaIgual = (prendaGuardada.id === prendaActual.id && prendaGuardada.colorPrenda === prendaActual.colorPrenda);

        return !prendaIgual;
    });

    aPrendasRecientes.unshift(prendaActual);

    if (aPrendasRecientes.length > 8) {
        aPrendasRecientes.pop();
    }

    localStorage.setItem('prendasRecientes', JSON.stringify(aPrendasRecientes));

}

document.addEventListener('DOMContentLoaded', function() {
    const formAsistente = document.getElementById('formAsistenteTalla');
    
    if (formAsistente) {
        formAsistente.addEventListener('submit', function(e) {
            e.preventDefault(); 

            const altura = document.getElementById('altura').value;
            const peso = document.getElementById('peso').value;
            const complexion = document.getElementById('complexion').value;
            const ajuste = document.getElementById('ajuste').value;
            const nombrePrenda = document.getElementById('nombrePrenda').value;

            const botonCalcular = document.getElementById('btnCalcularTalla');
            const textoOriginal = botonCalcular.innerText;
            botonCalcular.disabled = true;
            botonCalcular.innerHTML = '<span class="spinner-border spinner-border-sm"></span> PENSANDO...';

            const contenedorResultado = document.getElementById('resultadoAsistenteTalla');

            fetch('controllers/apiAsistenteTallasController.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ altura, peso, complexion, ajuste, prenda: nombrePrenda })
            })
            .then(response => response.json())
            .then(data => {
                botonCalcular.disabled = false;
                botonCalcular.innerText = textoOriginal;

                console.log(data);

                if(data.talla) {
                    contenedorResultado.innerHTML = `
                        <h4 class="fw-bold text-uppercase mb-2" style="letter-spacing: 2px;">
                            Talla Recomendada: <span style="font-size: 1.5em; text-decoration: underline;">${data.talla}</span>
                        </h4>
                        <p class="mb-0 small text-muted">${data.explicacion}</p>
                    `;
                    contenedorResultado.classList.remove('d-none');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Ups...',
                        text: data.message || 'La IA no pudo calcular tu talla en este momento.',
                        confirmButtonColor: '#000'
                    });
                }
            })
            .catch(error => {
                botonCalcular.disabled = false;
                botonCalcular.innerText = textoOriginal;
                console.error("Error:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No hemos podido contactar con el asistente.',
                    confirmButtonColor: '#000'
                });
            });
        });
    }
});

