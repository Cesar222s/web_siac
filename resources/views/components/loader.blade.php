<!-- Loading Component -->
<div id="page-loader" style="position:fixed; inset:0; background:var(--bg); z-index:9999; display:flex; align-items:center; justify-content:center; flex-direction:column; gap:1.5rem; transition:opacity 0.5s ease, visibility 0.5s ease;">
    <img src="{{ asset('images/loader.svg') }}" alt="Cargando..." style="width:80px; height:80px;">
    <div style="text-align:center;">
        <div style="font-size:1.2rem; font-weight:600; background:linear-gradient(120deg,var(--primary),var(--accent)); -webkit-background-clip:text; background-clip:text; color:transparent; margin-bottom:0.5rem;">
            Cargando SIAC...
        </div>
        <div style="font-size:0.9rem; color:var(--text-dim);">
            Preparando tu experiencia de conducción segura
        </div>
    </div>
</div>

<script>
// Ocultar loader cuando la página termine de cargar
window.addEventListener('load', function() {
    const loader = document.getElementById('page-loader');
    if (loader) {
        setTimeout(function() {
            loader.style.opacity = '0';
            loader.style.visibility = 'hidden';
            setTimeout(function() {
                loader.remove();
            }, 500);
        }, 500);
    }
});
</script>
