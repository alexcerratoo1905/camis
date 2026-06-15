document.addEventListener("DOMContentLoaded", function () {
    const sliderMin = document.getElementById("slider-min");
    const sliderMax = document.getElementById("slider-max");
    const minVal = document.getElementById("precio-min-val");
    const maxVal = document.getElementById("precio-max-val");
    const sliderTrack = document.querySelector(".slider-track");
    
    if (!sliderMin || !sliderMax) return;
    
    const gap = 1;

    function updateSliderTrack() {
        const min = parseInt(sliderMin.value);
        const max = parseInt(sliderMax.value);
        const minAttr = parseInt(sliderMin.min);
        const maxAttr = parseInt(sliderMax.max);
        
        const percent1 = ((min - minAttr) / (maxAttr - minAttr)) * 100;
        const percent2 = ((max - minAttr) / (maxAttr - minAttr)) * 100;
        
        sliderTrack.style.left = percent1 + "%";
        sliderTrack.style.width = (percent2 - percent1) + "%";
    }

    function controlarSliders(event) {
        let valorMin = parseInt(sliderMin.value);
        let valorMax = parseInt(sliderMax.value);
                     
        if (valorMax - valorMin <= gap) {
            if (event.target === sliderMin) {
                sliderMin.value = valorMax - gap;
            } else {
                sliderMax.value = valorMin + gap;
            }
        }
        
        minVal.textContent = sliderMin.value;
        maxVal.textContent = sliderMax.value;
        updateSliderTrack();
    }

    sliderMin.addEventListener("input", controlarSliders);
    sliderMax.addEventListener("input", controlarSliders);
    
    updateSliderTrack();
});

function aplicarFiltroPrecio() {
    let min = document.getElementById("slider-min").value;
    let max = document.getElementById("slider-max").value;
    
    let urlParams = new URLSearchParams(window.location.search);
    
    urlParams.set('precioMin', min);
    urlParams.set('precioMax', max);
    
    urlParams.delete('pagina');
    
    window.location.href = 'catalogo.php?' + urlParams.toString();
}