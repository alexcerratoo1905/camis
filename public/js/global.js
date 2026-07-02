document.addEventListener("DOMContentLoaded", function () {

    let promoModalElement = document.getElementById('promoModal');

    if (promoModalElement) {

        let horaLocalStore = localStorage.getItem("tiempoModal");

        let horaActual = Date.now();

        if (!horaLocalStore || (horaActual - horaLocalStore) > 18000000) {

            setTimeout(function () {
                let miModal = new bootstrap.Modal(document.getElementById('promoModal'));

                miModal.show();

                localStorage.setItem('tiempoModal', Date.now());
            }, 3000);
        }
    }

    inicializarBuscadorEnVivo();

    let graciasCompra = document.getElementById("graciasCompra");

    if (graciasCompra) {
        setTimeout(function () {
            window.location.href = "perfil.php?seccion=pedidos";
        }, 4000);
    }

});

function inicializarBuscadorEnVivo() {
    let prendaABuscar = document.getElementById("inputBuscador");
    let contenedorResultados = document.getElementById("cajaResultados");

    if (prendaABuscar && contenedorResultados) {
        prendaABuscar.addEventListener("input", function () {
            let texto = this.value.trim();

            if (texto.length >= 2) {
                fetch('controllers/apiBuscarController.php?q=' + texto + '&apiKey=Z5qbS7OXmHrLOoxa8BXWEDxjh5P5qffBQHCH9aV2mDrWtXxTcKsFssJvwFc4Todc')
                    .then(respuesta => respuesta.json())
                    .then(datos => {
                        if (datos.length > 0) {
                            contenedorResultados.innerHTML = '';

                            datos.forEach(producto => {
                                let htmlProducto = `
                                    <a href="fichaProducto.php?idPrenda=${producto.id}&color=${producto.color_id}" class="text-decoration-none text-dark d-block p-3 border-bottom bg-white" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='#fff'">
                                        <div class="d-flex align-items-center">
                                            <img src="${producto.url_imagen}" style="width: 50px; height: 50px; object-fit: cover;" class="me-3 border border-dark border-1">
                                            <div>
                                                <span class="fw-bold d-block text-uppercase" style="font-size: 0.9rem;">${producto.nombre}</span>
                                                <span class="text-muted fw-bold">${producto.precio} €</span>
                                            </div>
                                        </div>
                                    </a>
                                `;
                                contenedorResultados.innerHTML += htmlProducto;
                            });

                            contenedorResultados.classList.remove('d-none');
                        } else {
                            contenedorResultados.innerHTML = '<div class="p-4 text-center text-muted fw-bold text-uppercase">No se encontraron prendas</div>';
                        }
                    });

            } else {
                contenedorResultados.innerHTML = "";
                contenedorResultados.classList.add('d-none');
            }
        });
    }
}
document.addEventListener("DOMContentLoaded", function () {

    const swalRapido = Swal.mixin({
        confirmButtonColor: 'var(--color-principal, #000)',
        showClass: {
            popup: 'animate__animated animate__fadeIn animate__faster' 
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOut animate__faster'
        }
    });

    const urlParams = new URLSearchParams(window.location.search);

    if (urlParams.has('mensaje')) {
        const mensaje = urlParams.get('mensaje');
        
        if (mensaje === 'registro_exito') {
            swalRapido.fire({ icon: 'success', title: '¡Bienvenido a HERROR!', text: 'Tu cuenta se ha creado con éxito. Ya puedes iniciar sesión.' });
        } else if (mensaje === 'login_requerido') {
            swalRapido.fire({ icon: 'info', title: '¡Inicia Sesión!', text: 'Por favor, debes iniciar sesión o registrarte antes de poder tramitar tu pedido.' });
        } else if (mensaje === 'passwordActualizada') {
            swalRapido.fire({ icon: 'success', title: '¡Contraseña actualizada!', text: 'Tu contraseña se ha cambiado correctamente. Ya puedes iniciar sesión.' });
        } else if (mensaje === 'codigo_enviado') {
            swalRapido.fire({ icon: 'success', title: '¡Revisa tu bandeja!', text: 'Te acabamos de enviar tu código del 10% de descuento.' });
        } else if (mensaje === 'error_peso') {
            swalRapido.fire({ icon: 'error', title: '¡Foto demasiado grande!', text: 'La imagen que intentas subir pesa más de 2MB. Por favor, recórtala o usa una foto más pequeña.' });
        } else if (mensaje === 'error_subida') {
            swalRapido.fire({ icon: 'error', title: 'Error de servidor', text: 'No se pudo guardar la imagen. Revisa los permisos de la carpeta.' });
        } else if (mensaje === 'prenda_subida') {
            swalRapido.fire({ icon: 'success', title: '¡Prenda Subida!', text: 'Tu prenda se ha publicado correctamente.' });
        } else if (mensaje === 'estado_actualizado') {
            swalRapido.fire({ icon: 'success', title: '¡Actualizado!', text: 'El estado del pedido se ha modificado correctamente.' });
        }
    }

    if (urlParams.has('error')) {
        const error = urlParams.get('error');
        
        if (error === 'registro_fallo') {
            swalRapido.fire({ icon: 'error', title: 'Ups...', text: 'Hubo un problema al registrarte. El correo ya está en uso.' });
        } else if (error === 'password_debil') {
            swalRapido.fire({ icon: 'warning', title: 'Contraseña poco segura', text: 'La contraseña debe tener al menos 8 caracteres, incluir una mayúscula, una minúscula y un número.' });
        } else if (error === 'falta_talla') {
            swalRapido.fire({ icon: 'warning', title: '¡Falta la talla!', text: 'Por favor, selecciona una talla antes de añadir al carrito.' });
        } else if (error === 'no_stock') {
            swalRapido.fire({ icon: 'error', title: 'Límite de stock', text: 'No hay más unidades disponibles de este artículo en tu talla y color.' });
        } else if (error === 'debes_iniciar_sesion' || error === 'acceso_denegado') {
            swalRapido.fire({ icon: 'warning', title: 'ACCESO RESTRINGIDO', text: 'Debes iniciar sesión antes.', borderRadius: '0' });
        } else if (error === 'codigo_invalido') {
            swalRapido.fire({ icon: 'error', title: 'CÓDIGO NO VÁLIDO', text: 'El código introducido es incorrecto o no pertenece a tu cuenta.' });
        } else if (error === 'codigo_usado') {
            swalRapido.fire({ icon: 'info', title: 'CÓDIGO AGOTADO', text: 'Este código ya ha sido utilizado anteriormente.' });
        } else if (error === 'codigo_existente') { 
            swalRapido.fire({ icon: 'warning', title: 'Ya estás en la lista', text: 'Este correo ya ha recibido un código de bienvenida anteriormente.' });
        }else if (error === "noAdmin"){
            swalRapido.fire({ icon: 'warning', title: 'No eres admin', text: 'No tienes acceso, no eres administrador' });
        }
    }

    if (typeof bienvenidoAlerta !== 'undefined') {
        if (bienvenidoAlerta === 'true') {
            swalRapido.fire({
                icon: 'success',
                title: '¡Hola, ' + (typeof nombreUsuario !== 'undefined' ? nombreUsuario : '') + '!',
                text: 'Has iniciado sesión correctamente.',
                timer: 3000,
                showConfirmButton: false
            });
        } else if (bienvenidoAlerta === 'false') {
            swalRapido.fire({ icon: 'error', title: 'Error de acceso', text: 'El email o la contraseña son incorrectos.' });
        }
    }

    if (typeof sesionCerradaAlerta !== 'undefined' && sesionCerradaAlerta === 'true') {
        swalRapido.fire({ icon: 'info', title: '¡Hasta pronto!', text: 'Has cerrado sesión correctamente de forma segura.', timer: 3000, showConfirmButton: false });
    }

    if (urlParams.has('mensaje') || urlParams.has('error')) {
        urlParams.delete('mensaje');
        urlParams.delete('error');
        const parametrosRestantes = urlParams.toString() ? '?' + urlParams.toString() : '';
        window.history.replaceState(null, null, window.location.pathname + parametrosRestantes);
    }
});



document.addEventListener("DOMContentLoaded", function () {

    document.addEventListener('click', function (e) {
        let botonClick = e.target.closest('.btn-toggle-favorito');

        if (botonClick) {
            e.preventDefault();

            let idPrenda = botonClick.getAttribute('data-id');
            let idColor = botonClick.getAttribute('data-color');

            let icono = botonClick.querySelector('i');

            let datos = new FormData();

            datos.append('idPrenda', idPrenda);
            datos.append('idColor', idColor);

            fetch('controllers/apiFavoritosController.php', {
                method: 'POST',
                body: datos
            })
                .then(respuesta => respuesta.json())
                .then(datos => {

                    if (datos.exito === false && datos.mensaje === 'noLogin') {
                        Swal.fire({
                            icon: 'info',
                            title: '¡Inicia Sesión!',
                            text: 'Debes iniciar sesión para guardar tus prendas favoritas.',
                            confirmButtonColor: 'var(--color-principal, #000)'
                        });
                        return;
                    }

                    if (datos.exito === true) {

                        let comboMemoria = idPrenda + '-' + idColor;

                        if (datos.accion === 'agregado') {
                            icono.classList.remove('bi-heart');
                            icono.classList.add('bi-heart-fill');
                            icono.classList.add('text-danger');
                            botonClick.classList.add('border-danger');

                            if (typeof listaFavoritosJS !== 'undefined') {
                                listaFavoritosJS.push(comboMemoria);
                            }
                        }
                        else if (datos.accion === 'eliminado') {

                            icono.classList.remove('bi-heart-fill');
                            icono.classList.remove('text-danger');
                            botonClick.classList.remove('border-danger');
                            icono.classList.add('bi-heart');

                            if (typeof listaFavoritosJS !== 'undefined') {
                                let indice = listaFavoritosJS.indexOf(comboMemoria);
                                if (indice !== -1) {
                                    listaFavoritosJS.splice(indice, 1);
                                }
                            }

                            if (window.location.href.includes('seccion=favoritos')) {

                                let columnaContenedora = botonClick.closest('.col-6');

                                if (columnaContenedora) {
                                    columnaContenedora.style.transition = 'all 0.6s ease';
                                    columnaContenedora.style.opacity = '0';
                                    columnaContenedora.style.transform = 'scale(0.5) translateY(50px)';

                                    setTimeout(function () {
                                        columnaContenedora.remove();
                                        let prendasRestantes = document.querySelectorAll('.btn-toggle-favorito').length;

                                        if (prendasRestantes === 0) {
                                            window.location.reload();
                                        }
                                    }, 600);
                                }
                            }
                        }
                    }

                })
                .catch(error => {
                    console.error("Error en la petición:", error);
                });

        }

    });
});


function pintarPrendasRecientes() {
    const carruselInner = document.getElementById('carruselRecientesInner');
    const seccionRecientes = document.getElementById('seccionRecientes');

    if (!carruselInner || !seccionRecientes) return;

    let aPrendasRecientes = [];
    let datosLocal = localStorage.getItem('prendasRecientes');

    if (datosLocal) {
        try {
            aPrendasRecientes = JSON.parse(datosLocal);
        } catch (error) {
            aPrendasRecientes = [];
        }
    }

    if (aPrendasRecientes.length > 0) {
        seccionRecientes.classList.remove("d-none");
    } else {
        return;
    }

    carruselInner.innerHTML = "";
    let htmlAcumulado = "";


    aPrendasRecientes.forEach((prenda, index) => {

        if (index % 4 === 0) {
            let activeClass = index === 0 ? "active" : "";
            htmlAcumulado += `<div class="carousel-item ${activeClass}" data-bs-interval="4000"><div class="row">`;
        }

        let iconoCorazon = 'bi-heart';
        let claseBordeDanger = '';
        if (typeof listaFavoritosJS !== 'undefined' && listaFavoritosJS.includes(prenda.id + '-' + prenda.colorPrenda)) {
            iconoCorazon = 'bi-heart-fill text-danger';
            claseBordeDanger = 'border-danger';
        }

        let rebaja = 0;
        if (prenda.rebaja && !isNaN(prenda.rebaja) && prenda.rebaja !== "0" && prenda.rebaja !== "") {
            rebaja = parseInt(prenda.rebaja);
        }

        let precioNormal = parseFloat((prenda.precio || '0').toString().replace(',', '.'));
        let htmlPrecio = `<p class="card-text mb-0 fw-bold text-dark">${precioNormal.toFixed(2)} €</p>`;
        let htmlBadge = '';


        if (rebaja > 0) {
            
            let precioFinal = precioNormal - (precioNormal * rebaja / 100);
            
            htmlBadge = `<span class="position-absolute top-0 end-0 m-2 badge bg-dark text-white rounded-0 fw-bold px-2 py-1 shadow-sm" style="font-size: 0.75rem; letter-spacing: 1px; z-index: 10;">-${rebaja}%</span>`;
            
            htmlPrecio = `
                <p class="card-text mb-0">
                    <span class="text-muted text-decoration-line-through small me-2">${precioNormal.toFixed(2)} €</span>
                    <span class="fw-bold text-dark">${precioFinal.toFixed(2)} €</span>
                </p>
            `;
        }

        htmlAcumulado += `
            <div class="col-6 col-md-3 position-relative d-flex flex-column mb-4">
                <div class="card product-card border-0 bg-transparent position-relative">
                    
                    <div class="img-wrapper position-relative overflow-hidden shadow-sm rounded-3">
                        
                        <a href="fichaProducto.php?idPrenda=${prenda.id}&color=${prenda.colorPrenda}" class="text-decoration-none text-dark d-block">
                            ${htmlBadge}
                            <img src="${prenda.imagen}" class="card-img-top rounded-0 img-fluida-reciente" alt="${prenda.nombre}">
                        </a>
                        
                        <div id="overlay-tallas-${prenda.id}" class="overlay-tallas d-none position-absolute bottom-0 start-0 w-100 bg-white bg-opacity-75 p-3 text-center" onclick="event.preventDefault();">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small fw-bold text-uppercase" style="letter-spacing: 1px;">Selecciona Talla</span>
                                <button type="button" class="btn-close" style="font-size: 0.7rem;" onclick="cerrarOverlayTallas(event, ${prenda.id})"></button>
                            </div>
                            <div id="contenedor-botones-${prenda.id}" class="d-flex justify-content-center flex-wrap gap-2">
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body text-center px-0 pb-1 mt-3">
                        <a href="fichaProducto.php?idPrenda=${prenda.id}&color=${prenda.colorPrenda}" class="text-decoration-none text-dark d-block">
                            <h5 class="card-title text-uppercase fw-bold fs-6 mb-1 text-truncate" style="letter-spacing: 0.5px;">${prenda.nombre}</h5>
                            ${htmlPrecio}
                        </a>
                    </div>
                </div>
                
                <div class="d-flex align-items-center justify-content-between gap-2 mt-2 px-1">
                    <button type="button" class="btn btn-outline-dark rounded-pill flex-grow-1 text-uppercase fw-bold"
                        style="height: 38px; font-size: 0.7rem; letter-spacing: 1px; transition: all 0.3s;"
                        onclick="abrirOverlayTallas(event, ${prenda.id}, ${prenda.colorPrenda})">
                        Añadir <i class="bi bi-plus-lg ms-1"></i>
                    </button>
                    
                    <button type="button" class="btn btn-toggle-favorito btn-favorito-custom btn-favorito-std d-flex justify-content-center align-items-center rounded-circle m-0 ${claseBordeDanger}"
                        style="border-color: #000; width: 38px; height: 38px;"
                        data-id="${prenda.id}"
                        data-color="${prenda.colorPrenda}">
                        <i class="bi ${iconoCorazon}"></i>
                    </button>
                </div>
            </div>
        `;

        if (index % 4 === 3 || index === aPrendasRecientes.length - 1) {
            htmlAcumulado += `</div></div>`;
        }
    });

    carruselInner.innerHTML = htmlAcumulado;
}

// =========================================================
// NUEVA LÓGICA DE AÑADIDO DIRECTO AL CARRITO (DROPSHIPPING)
// =========================================================

function abrirOverlayTallas(event, idPrenda, idColor) {
    event.preventDefault();

    const tarjeta = event.target.closest('.col-6') || event.target.closest('.product-card');
    if (!tarjeta) return;

    const overlay = tarjeta.querySelector('.overlay-tallas');
    const contenedor = tarjeta.querySelector('[id^="contenedor-botones-"]');

    if (!overlay || !contenedor) return;

    overlay.classList.remove('d-none');
    contenedor.innerHTML = ''; // Limpiamos para que no salga "Cargando..."

    // Hardcodeamos las tallas disponibles de Dropshipping (Instantáneo)
    const tallasFijas = [
        { id: 'S', label: 'S' },
        { id: 'M', label: 'M' },
        { id: 'L', label: 'L' },
        { id: 'XL', label: 'XL' },
        { id: '2XL', label: '2XL (+1€)' },
        { id: '3XL', label: '3XL (+1€)' },
        { id: '4XL', label: '4XL (+1€)' }
    ];

    tallasFijas.forEach(tallaObj => {
        let btn = document.createElement('button');
        btn.className = 'btn btn-outline-dark rounded-0 px-2 py-1 fw-bold m-1';
        btn.style.fontSize = '0.8rem';
        btn.textContent = tallaObj.label;

        // Al hacer click, manda directamente la talla real ('2XL') al carrito
        btn.onclick = (e) => anadirDirectoCarrito(e, idPrenda, idColor, tallaObj.id);

        contenedor.appendChild(btn);
    });
}

function cerrarOverlayTallas(event, idPrenda) {
    event.preventDefault();
    const tarjeta = event.target.closest('.col-6') || event.target.closest('.product-card');
    if (tarjeta) {
        const overlay = tarjeta.querySelector('.overlay-tallas');
        if (overlay) overlay.classList.add('d-none');
    }
}

function anadirDirectoCarrito(event, idPrenda, idColor, talla) {
    event.preventDefault();

    let datos = new FormData();
    datos.append('accion', 'agregar');
    datos.append('idPrenda', idPrenda);
    datos.append('color_id', idColor);
    datos.append('talla', talla);

    cerrarOverlayTallas(event, idPrenda);

    fetch('controllers/carritoController.php', {
        method: 'POST',
        body: datos
    })
        .then(respuesta => {
            let iconoCesta = document.getElementById('contador-carrito');
            if (iconoCesta) {
                iconoCesta.innerText = parseInt(iconoCesta.innerText || 0) + 1;
            }
            Swal.fire({
                icon: 'success',
                title: '¡Añadido al carrito!',
                text: `Talla ${talla} añadida correctamente.`,
                confirmButtonColor: 'var(--color-principal, #000)',
                timer: 2000,
                showConfirmButton: false,
                showClass: {
                    popup: 'animate__animated animate__fadeIn animate__faster'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOut animate__faster'
                }
            });
        })
        .catch(error => {
            console.error("Error al añadir:", error);
            Swal.fire({
                icon: 'error',
                title: 'Ups...',
                text: 'Hubo un problema al añadir la prenda.',
                confirmButtonColor: 'var(--color-principal, #000)',
                showClass: {
                    popup: 'animate__animated animate__fadeIn animate__faster'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOut animate__faster'
                }
            });
        });
}

document.addEventListener("DOMContentLoaded", function () {
    pintarPrendasRecientes();
});

document.addEventListener('DOMContentLoaded', function() {
    const contenedorPrendaSubida = document.getElementById('prendaSubida');
    if (contenedorPrendaSubida) {
        setTimeout(function() {
            window.location.href = 'perfil.php?mensaje=prenda_subida';
        }, 4000); 
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const chatbotToggle = document.getElementById("chatbot-toggle");
    const chatbotWindow = document.getElementById("chatbot-window");
    const chatbotClose = document.getElementById("chatbot-close");
    const chatbotForm = document.getElementById("chatbot-form");
    const chatbotInput = document.getElementById("chatbot-input");
    const chatbotMessages = document.getElementById("chatbot-messages");

    if (chatbotToggle && chatbotWindow) {
        
        chatbotToggle.addEventListener("click", () => {
            chatbotWindow.classList.toggle("d-none");
            if (!chatbotWindow.classList.contains("d-none")) {
                chatbotInput.focus();
            }
        });

        chatbotClose.addEventListener("click", () => {
            chatbotWindow.classList.add("d-none");
        });

        function addMessage(text, sender) {
            const msgDiv = document.createElement("div");
            msgDiv.classList.add("chat-msg");
            
            if (sender === "user") {
                msgDiv.classList.add("user-msg");
            } else {
                msgDiv.classList.add("bot-msg");
                text = text.replace(/\n/g, "<br>");
            }
            
            msgDiv.innerHTML = text;
            chatbotMessages.appendChild(msgDiv);
            
            chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
        }

        chatbotForm.addEventListener("submit", function (e) {
            e.preventDefault(); 
            
            const mensaje = chatbotInput.value.trim();
            if (mensaje === "") return;

            chatbotInput.value = "";
            addMessage(mensaje, "user");

            const botLoadingDiv = document.createElement("div");
            botLoadingDiv.classList.add("chat-msg", "bot-msg", "loading-msg");
            botLoadingDiv.innerHTML = '<i>Pensando...</i>';
            chatbotMessages.appendChild(botLoadingDiv);
            chatbotMessages.scrollTop = chatbotMessages.scrollHeight;

            fetch('controllers/apiChatBotController.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ mensaje: mensaje })
            })
            .then(res => res.json())
            .then(data => {
                botLoadingDiv.remove();
                
                if (data.status === "success") {
                    addMessage(data.respuesta, "bot");
                } else {
                    addMessage("Error: " + data.message, "bot");
                }
            })
            .catch(error => {
                botLoadingDiv.remove();
                console.error("Error en Chatbot:", error);
                addMessage("Lo siento, he perdido la conexión. Inténtalo de nuevo.", "bot");
            });
        });
    }
});