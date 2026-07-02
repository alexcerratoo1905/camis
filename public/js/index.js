// =====================================================================
// HERROR - EFECTO SCROLL 3D PARA MÚLTIPLES CAMISETAS (HARDWARE ACCELERATED)
// =====================================================================

document.addEventListener("DOMContentLoaded", function() {
    
    const shirts = document.querySelectorAll(".floating-shirt");
    const container = document.getElementById("shirt-container");
    
    if (shirts.length === 0 || !container) return;

    let currentScrollY = window.pageYOffset || document.documentElement.scrollTop;
    
    function render3D() {
        // Obtenemos el scroll directamente en cada fotograma, no dependemos del evento "scroll"
        let targetScrollY = window.pageYOffset || document.documentElement.scrollTop;
        
        // Interpolación fluida
        currentScrollY += (targetScrollY - currentScrollY) * 0.08;
        
        shirts.forEach((shirt, index) => {
            let speed = parseFloat(shirt.getAttribute('data-speed'));
            let rotSpeed = parseFloat(shirt.getAttribute('data-rot'));
            let dir = parseFloat(shirt.getAttribute('data-dir')); // 1 o -1
            
            // Usamos translate3d para forzar la aceleración GPU y evitar lag
            let translateY = currentScrollY * speed * dir * -1; 
            let rotateY = (currentScrollY * rotSpeed) + (index * 35); 
            let rotateZ = currentScrollY * (rotSpeed * 0.1) * dir;
            
            shirt.style.transform = `translate3d(0px, ${translateY}px, 0px) rotateY(${rotateY}deg) rotateZ(${rotateZ}deg)`;
        });

        // Bajar opacidad al llegar muy abajo para no distraer los carruseles
        let globalOpacity = Math.max(0.1, 1 - (currentScrollY * 0.0003));
        container.style.opacity = globalOpacity;

        requestAnimationFrame(render3D);
    }

    // Iniciar loop de animación
    requestAnimationFrame(render3D);

});