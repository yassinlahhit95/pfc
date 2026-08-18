<div class="legal-hero">
    <h1><i class="fas fa-clipboard-check" style="margin-right:10px;opacity:.9;"></i><?= __('Política de Gestión', 'Política de Gestión') ?></h1>
    <span class="badge">Calidad del Servicio · Seguridad · Continuidad</span>
</div>

<main class="legal-main">

    <div class="legal-toc">
        <h2><i class="fas fa-list-ul" style="margin-right:6px;"></i> <?= __('Contenido', 'Contenido') ?></h2>
        <ol>
            <li><a href="#objetivo">Objetivo y alcance</a></li>
            <li><a href="#sla">Disponibilidad del servicio (SLA)</a></li>
            <li><a href="#seguridad">Seguridad de la información</a></li>
            <li><a href="#datos">Gestión de datos y copias de seguridad</a></li>
            <li><a href="#incidencias">Gestión de incidencias</a></li>
            <li><a href="#actualizaciones">Actualizaciones y mantenimiento</a></li>
            <li><a href="#responsabilidades">Responsabilidades</a></li>
            <li><a href="#mejora">Mejora continua</a></li>
        </ol>
    </div>

    <section class="legal-section" id="objetivo">
        <h2><i class="fas fa-bullseye"></i> 1. Objetivo y Alcance</h2>
        <p>La presente <strong>Política de Gestión</strong> describe los compromisos de calidad, seguridad y continuidad de la plataforma <strong><?= htmlspecialchars(FeatureGuard::getCenterName()) ?></strong> — sistema de gestión escolar proporcionado a centros educativos.</p>
    </section>

    <section class="legal-section" id="sla">
        <h2><i class="fas fa-gauge-high"></i> 2. Disponibilidad del Servicio (SLA)</h2>
        <div class="sla-grid">
            <div class="sla-card">
                <span class="val">99,5%</span>
                <span class="lbl">Disponibilidad mensual</span>
            </div>
            <div class="sla-card">
                <span class="val">&lt; 4h</span>
                <span class="lbl">Respuesta a incidencia crítica</span>
            </div>
            <div class="sla-card">
                <span class="val">24h</span>
                <span class="lbl">Ventana de mantenimiento</span>
            </div>
            <div class="sla-card">
                <span class="val">Diaria</span>
                <span class="lbl">Copias de seguridad</span>
            </div>
        </div>
    </section>

    <section class="legal-section" id="seguridad">
        <h2><i class="fas fa-shield-alt"></i> 3. Seguridad de la Información</h2>
        <p><?= htmlspecialchars(FeatureGuard::getCenterName()) ?> implementa medidas de seguridad avanzadas: control de accesos mediante hash bcrypt, doble factor de autenticación (2FA) para roles altos, geobloqueo de IP internacionales para administradores, cookies seguras, protocolo HTTPS/TLS, consultas preparadas SQL para evitar inyecciones, prevención de XSS y CSRF.</p>
    </section>

    <section class="legal-section" id="datos">
        <h2><i class="fas fa-hard-drive"></i> 4. Gestión de Datos y Copias de Seguridad</h2>
        <p>Las copias de seguridad se realizan a diario y se guardan cifradas en servidores aislados durante un mínimo de 30 días.</p>
    </section>

    <section class="legal-section" id="incidencias">
        <h2><i class="fas fa-triangle-exclamation"></i> 5. Gestión de Incidencias</h2>
        <p>Las incidencias se clasifican según gravedad y cuentan con plazos máximos de resolución (desde 4 horas para incidentes críticos hasta 24 horas para incidencias menores).</p>
    </section>

    <section class="legal-section" id="actualizaciones">
        <h2><i class="fas fa-rotate"></i> 6. Actualizaciones y Mantenimiento</h2>
        <p>El mantenimiento programado se planifica fuera de las horas lectivas del centro para garantizar una experiencia óptima.</p>
    </section>

    <section class="legal-section" id="responsabilidades">
        <h2><i class="fas fa-handshake"></i> 7. Responsabilidades</h2>
        <p><?= htmlspecialchars(FeatureGuard::getCenterName()) ?> asume la responsabilidad técnica y del cifrado de datos; el centro educativo asume la gestión responsable de cuentas, contraseñas y permisos del personal.</p>
    </section>

    <section class="legal-section" id="mejora">
        <h2><i class="fas fa-chart-line"></i> 8. Mejora Continua</h2>
        <p>Realizamos auditorías de seguridad periódicas para evaluar la resistencia de la plataforma frente a ciberamenazas externas.</p>
        <p style="color:var(--mut);font-size:.85rem;margin-top:16px;">Última actualización: <?= date('d/m/Y') ?></p>
    </section>

</main>
