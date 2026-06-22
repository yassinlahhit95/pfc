<footer class="legal-footer">
    <div class="legal-footer-inner">
        <div class="legal-foot-top">
            <a href="/" class="legal-brand">
                <svg class="legal-brand-logo" viewBox="0 0 40 40"><use href="#ap-logo"/></svg>
                Aula<b>Pro</b>
            </a>
            <p class="legal-foot-tagline">Gestión integral para centros de Formación Profesional.</p>
        </div>
        <nav class="legal-footer-links">
            <a href="/vistas/legal/aviso-legal.php">Aviso Legal</a>
            <a href="/vistas/legal/politica-de-privacidad.php">Política de Privacidad</a>
            <a href="/vistas/legal/politica-de-cookies.php">Política de Cookies</a>
            <a href="/vistas/legal/politica-de-gestion.php">Política de Gestión</a>
        </nav>
        <div class="legal-foot-bot">
            <span>© <?= date('Y') ?> AulaPro · Todos los derechos reservados · Hecho en España</span>
            <button id="btn-top" aria-label="Volver arriba" onclick="window.scrollTo({top:0,behavior:'smooth'})">
                <i class="fas fa-chevron-up"></i>
            </button>
        </div>
    </div>
</footer>

<script>
window.addEventListener('scroll', function() {
    document.getElementById('btn-top').classList.toggle('visible', window.scrollY > 300);
});
</script>
</body>
</html>
