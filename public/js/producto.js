const mainProducto = document.getElementById("mainProducto");

// Inicializar Tooltips de Bootstrap (Para la versión Player)
document.addEventListener("DOMContentLoaded", function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

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
        if (nuevoIndex >= miniaturasVisibles.length) nuevoIndex = 0;
    } else {
        nuevoIndex = indexActual - 1;
        if (nuevoIndex < 0) nuevoIndex = miniaturasVisibles.length - 1;
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
            if (!primeraVisible) primeraVisible = miniatura;
        } else {
            miniatura.style.display = 'none';
        }
    });

    if (primeraVisible) primeraVisible.click();

    let inputColor = document.getElementById('input_color_id');
    if (inputColor) inputColor.value = colorId;

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
        if (primeraVisible) mainProducto.dataset.imagen = primeraVisible.src;
        // AQUÍ ES DONDE LLAMA A LA FUNCIÓN QUE AHORA SÍ EXISTE ABAJO
        if (typeof guardarPrendasRecientes === "function") guardarPrendasRecientes();
    }
}

// ----------------------------------------------------
// MAGIA DROPSHIPPING: CÁLCULO DE PRECIO EN TIEMPO REAL
// ----------------------------------------------------
function calcularPrecioFinal() {
    if(!mainProducto) return;
    
    let precioBase = parseFloat(mainProducto.dataset.precio);
    let rebaja = parseInt(mainProducto.dataset.rebaja) || 0;
    let precioConRebaja = precioBase - (precioBase * rebaja / 100);
    let extras = 0;
    
    const checkPlayer = document.getElementById('extra_player');
    const checkPantalon = document.getElementById('extra_pantalon');
    const checkParche = document.getElementById('tiene_parche');
    const checkPers = document.getElementById('tiene_personalizacion');
    const selectTalla = document.getElementById('talla');
    
    if(checkPlayer && checkPlayer.checked) extras += 3;
    if(checkPantalon && checkPantalon.checked) extras += 10;
    if(checkParche && checkParche.checked) extras += 1;
    if(checkPers && checkPers.checked) extras += 2;
    
    if(selectTalla && selectTalla.value) {
        if(['2XL', '3XL', '4XL'].includes(selectTalla.value)) {
            extras += 1;
        }
    }
    
    let precioTotal = precioConRebaja + extras;
    
    const contenedorPrecio = document.getElementById('precioFinalVisible');
    if(contenedorPrecio) {
        contenedorPrecio.innerText = precioTotal.toFixed(2).replace('.', ',') + ' €';
    }
}

document.addEventListener("DOMContentLoaded", function () {
    const urlParams = new URLSearchParams(window.location.search);
    const colorIdUrl = urlParams.get('color');
    let colorInicial = colorIdUrl ? document.querySelector('.color-swatch-wrapper[data-color-id="' + colorIdUrl + '"]') : document.querySelector('.color-swatch-wrapper');
    if (colorInicial) seleccionarColor(colorInicial.getAttribute('data-color-id'), colorInicial);

    document.querySelectorAll('.extra-checkbox').forEach(cb => {
        cb.addEventListener('change', calcularPrecioFinal);
    });
    
    const selectTalla = document.getElementById('talla');
    if(selectTalla) selectTalla.addEventListener('change', calcularPrecioFinal);

    const checkParche = document.getElementById('tiene_parche');
    const divParche = document.getElementById('div_texto_parche');
    if(checkParche) {
        checkParche.addEventListener('change', (e) => {
            if(e.target.checked) divParche.classList.remove('d-none');
            else divParche.classList.add('d-none');
        });
    }

    const checkPers = document.getElementById('tiene_personalizacion');
    const divPers = document.getElementById('div_texto_personalizacion');
    if(checkPers) {
        checkPers.addEventListener('change', (e) => {
            if(e.target.checked) divPers.classList.remove('d-none');
            else divPers.classList.add('d-none');
        });
    }
});

// Animación Carrito GSAP
if (typeof gsap !== 'undefined' && typeof MorphSVGPlugin !== 'undefined') {
    gsap.registerPlugin(MorphSVGPlugin);

    document.querySelectorAll('.add-to-cart').forEach(button => {
        let morph = button.querySelector('.morph path'),
            shirt = button.querySelectorAll('.shirt svg > path');

        button.addEventListener('click', e => {
            e.preventDefault();

            let selectTalla = button.closest('form').querySelector('#talla');
            if (selectTalla && (!selectTalla.value || selectTalla.value === "")) {
                Swal.fire({
                    icon: 'warning',
                    title: '¡Falta la talla!',
                    text: 'Por favor, selecciona una talla antes de añadir al carrito.',
                    confirmButtonColor: '#000'
                });
                return; 
            }

            if (button.classList.contains('active')) { return; }
            button.classList.add('active');

            gsap.to(button, {
                keyframes: [{ '--background-scale': .97, duration: .15 }, { '--background-scale': 1, delay: .125, duration: 1.2, ease: 'elastic.out(1, .6)' }]
            });
            gsap.to(button, {
                keyframes: [{ '--shirt-scale': 1, '--shirt-y': '-42px', '--cart-x': '0px', '--cart-scale': 1, duration: .4, ease: 'power1.in' },
                            { '--shirt-y': '-40px', duration: .3 },
                            { '--shirt-y': '16px', '--shirt-scale': .9, duration: .25, ease: 'none' },
                            { '--shirt-scale': 0, duration: .3, ease: 'none' }]
            });
            gsap.to(button, { '--shirt-second-y': '0px', delay: .835, duration: .12 });
            gsap.to(button, {
                keyframes: [{ '--cart-clip': '12px', '--cart-clip-x': '3px', delay: .9, duration: .06 },
                            { '--cart-y': '2px', duration: .1 },
                            { '--cart-tick-offset': '0px', '--cart-y': '0px', duration: .2, onComplete() { button.style.overflow = 'hidden'; } },
                            { '--cart-x': '52px', '--cart-rotate': '-15deg', duration: .2 },
                            { '--cart-x': '200px', '--cart-rotate': '0deg', duration: .3, clearProps: true, onComplete() {
                                button.style.overflow = 'hidden';
                                button.style.setProperty('--text-o', 0);
                                button.style.setProperty('--text-x', '0px');
                                button.style.setProperty('--cart-x', '-160px');
                            }},
                            { '--text-o': 1, '--text-x': '20px', '--cart-x': '-100px', '--cart-scale': .75, duration: .25, clearProps: true, onComplete() { button.classList.remove('active'); }}]
            });
            gsap.to(button, { keyframes: [{ '--text-o': 0, duration: .3 }] });
            gsap.to(morph, {
                keyframes: [{ morphSVG: 'M0 12C6 12 20 10 32 0C43.9024 9.99999 58 12 64 12V13H0V12Z', duration: .25, ease: 'power1.out' },
                            { morphSVG: 'M0 12C6 12 17 12 32 12C47.9024 12 58 12 64 12V13H0V12Z', duration: .15, ease: 'none' }]
            });

            setTimeout(() => { button.closest('form').submit(); }, 1900);
        });
    });
}

// ====================================================================
// FUNCIÓN RECUPERADA: GUARDAR PRENDAS VISTAS EN EL LOCALSTORAGE
// ====================================================================
function guardarPrendasRecientes() {
    const main = document.getElementById("mainProducto");
    if (!main) return;

    const id = main.dataset.id;
    const nombre = main.dataset.nombre;
    const precio = main.dataset.precio;
    const rebaja = main.dataset.rebaja || "0";
    const imagen = main.dataset.imagen;
    const colorPrenda = main.dataset.colorPrenda;

    if (!id || !imagen || !colorPrenda) return;

    let recientes = [];
    try {
        recientes = JSON.parse(localStorage.getItem("prendasRecientes")) || [];
    } catch (e) {
        recientes = [];
    }

    const nuevaPrenda = {
        id: id,
        nombre: nombre,
        precio: precio,
        rebaja: rebaja,
        imagen: imagen,
        colorPrenda: colorPrenda
    };

    // Evitar duplicados de la misma prenda con el mismo color
    recientes = recientes.filter(p => !(p.id === id && p.colorPrenda === colorPrenda));

    // Poner la nueva prenda la primera de la lista
    recientes.unshift(nuevaPrenda);

    // Limitar a un máximo de 12 prendas guardadas en el historial
    if (recientes.length > 12) {
        recientes.pop();
    }

    localStorage.setItem("prendasRecientes", JSON.stringify(recientes));

    // Refrescar el carrusel de abajo automáticamente
    if (typeof pintarPrendasRecientes === "function") {
        pintarPrendasRecientes();
    }
}