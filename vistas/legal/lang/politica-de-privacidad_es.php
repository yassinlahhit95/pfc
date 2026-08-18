<div class="legal-hero">
    <h1><i class="fas fa-user-shield" style="margin-right:10px;opacity:.9;"></i><?= __('Política de Privacidad', 'Política de Privacidad') ?></h1>
    <span class="badge">RGPD · LOPD-GDD · Art. 13 y 14</span>
</div>

<main class="legal-main">

    <div class="legal-toc">
        <h2><i class="fas fa-list-ul" style="margin-right:6px;"></i> <?= __('Contenido', 'Contenido') ?></h2>
        <ol>
            <li><a href="#responsable">Responsable del tratamiento</a></li>
            <li><a href="#datos">Datos que tratamos</a></li>
            <li><a href="#finalidades">Finalidades y base jurídica</a></li>
            <li><a href="#conservacion">Conservación de datos</a></li>
            <li><a href="#destinatarios">Destinatarios y transferencias</a></li>
            <li><a href="#derechos">Tus derechos</a></li>
            <li><a href="#menores">Protección de menores</a></li>
            <li><a href="#app-movil">Aplicación móvil</a></li>
            <li><a href="#seguridad">Seguridad</a></li>
            <li><a href="#cambios">Cambios en esta política</a></li>
        </ol>
    </div>

    <div class="legal-info-box verde">
        <i class="fas fa-check-circle"></i>
        <span>Nos comprometemos a tratar tus datos personales con total transparencia, en estricto cumplimiento del <strong>Reglamento (UE) 2016/679 (RGPD)</strong> y la <strong>Ley Orgánica 3/2018 (LOPD-GDD)</strong>.</span>
    </div>

    <?php
    $direccionCompleta = trim(($cfg['direccionCentro'] ?? '') . ', ' . ($cfg['ciudadCentro'] ?? '') . ' ' . ($cfg['cpCentro'] ?? ''), ", ");
    $nifCif = trim($cfg['nifCifCentro'] ?? '');
    ?>
    <section class="legal-section" id="responsable">
        <h2><i class="fas fa-building"></i> 1. Responsable del Tratamiento</h2>
        <ul>
            <li><strong>Identidad:</strong> <?= htmlspecialchars($nombreCentro) ?></li>
            <?php if ($nifCif !== ''): ?>
            <li><strong>NIF/CIF:</strong> <?= htmlspecialchars($nifCif) ?></li>
            <?php endif; ?>
            <?php if ($direccionCompleta !== ''): ?>
            <li><strong>Dirección postal:</strong> <?= htmlspecialchars($direccionCompleta) ?></li>
            <?php endif; ?>
            <?php if (!empty($cfg['telefonoCentro'])): ?>
            <li><strong>Teléfono:</strong> <?= htmlspecialchars($cfg['telefonoCentro']) ?></li>
            <?php endif; ?>
            <?php if (!empty($emailCentro)): ?>
            <li><strong>Contacto:</strong> <a href="mailto:<?= htmlspecialchars($emailCentro) ?>"><?= htmlspecialchars($emailCentro) ?></a></li>
            <?php endif; ?>
        </ul>
        <?php if (!empty($_SESSION['idAdmin']) && ($nifCif === '' || $direccionCompleta === '' || empty($cfg['telefonoCentro']) || empty($emailCentro))): ?>
        <p class="legal-info-box" style="margin-top:12px;background:var(--naranja-suave,#fffbeb);border-color:var(--naranja,#f59e0b);color:var(--naranja-ink,#92400e);">
            <i class="fas fa-triangle-exclamation"></i>
            Solo tú ves este aviso (sesión de administrador): faltan datos de contacto del responsable del tratamiento por configurar en Administración → Configuración del Centro antes de publicar o enlazar esta política públicamente.
        </p>
        <?php endif; ?>
        <p style="margin-top:12px;"><strong>Proveedor tecnológico (Encargado del tratamiento):</strong> <?= htmlspecialchars(FeatureGuard::getCenterName()) ?> SaaS, responsable del sistema de gestión escolar, actúa como encargado del tratamiento bajo contrato de confidencialidad con el centro, en los términos del Art. 28 RGPD.</p>
    </section>

    <section class="legal-section" id="datos">
        <h2><i class="fas fa-database"></i> 2. Datos que Tratamos</h2>
        <p>Dependiendo de tu relación con el centro, tratamos las siguientes categorías de datos:</p>

        <h3>Administradores y personal directivo</h3>
        <ul>
            <li>Nombre completo, correo electrónico y contraseña (en formato hash).</li>
            <li>Datos de actividad en la plataforma (registros de acceso, acciones realizadas).</li>
        </ul>

        <h3>Docentes (profesores)</h3>
        <ul>
            <li>Nombre completo, correo electrónico, contraseña (hash), foto de perfil (opcional).</li>
            <li>Asignación a módulos, ciclos y aulas.</li>
            <li>Calificaciones introducidas en la plataforma.</li>
        </ul>

        <h3>Estudiantes</h3>
        <ul>
            <li>Nombre completo, email, contraseña (hash), fecha de nacimiento, NIA/DNI.</li>
            <li>Ciclo formativo y curso asignado.</li>
            <li>Calificaciones, notas de módulos y proyectos fin de grado (TFG).</li>
            <li>Historial de pagos de matrícula.</li>
            <li>Retos académicos y asistencia.</li>
        </ul>

        <h3>Tutores legales</h3>
        <ul>
            <li>Nombre completo, correo electrónico, contraseña (hash), relación con el estudiante.</li>
            <li>Acceso a la información académica del estudiante tutelado.</li>
        </ul>
    </section>

    <section class="legal-section" id="finalidades">
        <h2><i class="fas fa-bullseye"></i> 3. Finalidades y Base Jurídica</h2>
        <p>El tratamiento de los datos se realiza con las siguientes finalidades y bases jurídicas (Art. 6 RGPD):</p>
        <table style="width:100%;border-collapse:collapse;font-size:.875rem;margin-top:12px;">
            <thead>
                <tr style="background:var(--bg);">
                    <th style="padding:10px 14px;text-align:left;border-bottom:2px solid var(--border);color:var(--mut);font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;">Finalidad</th>
                    <th style="padding:10px 14px;text-align:left;border-bottom:2px solid var(--border);color:var(--mut);font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;">Base jurídica</th>
                </tr>
            </thead>
            <tbody>
                <tr><td style="padding:10px 14px;border-bottom:1px solid var(--border);">Gestión académica (notas, módulos, horarios, retos)</td><td style="padding:10px 14px;border-bottom:1px solid var(--border);">Relación contractual / obligación legal educativa</td></tr>
                <tr><td style="padding:10px 14px;border-bottom:1px solid var(--border);">Comunicaciones internas entre usuarios de la plataforma</td><td style="padding:10px 14px;border-bottom:1px solid var(--border);">Interés legítimo / relación contractual</td></tr>
                <tr><td style="padding:10px 14px;border-bottom:1px solid var(--border);">Gestión de pagos de matrícula</td><td style="padding:10px 14px;border-bottom:1px solid var(--border);">Relación contractual</td></tr>
                <tr><td style="padding:10px 14px;border-bottom:1px solid var(--border);">Seguridad y control de acceso (logs de actividad)</td><td style="padding:10px 14px;border-bottom:1px solid var(--border);">Interés legítimo / obligación legal</td></tr>
            </tbody>
        </table>
    </section>

    <section class="legal-section" id="conservacion">
        <h2><i class="fas fa-clock"></i> 4. Conservación de Datos</h2>
        <p>Los datos se conservarán durante el tiempo necesario para la finalidad para la que fueron recogidos:</p>
        <ul>
            <li><strong>Datos académicos:</strong> Mínimo 5 años, conforme a la normativa educativa.</li>
            <li><strong>Datos de acceso y logs:</strong> Máximo 12 meses.</li>
            <li><strong>Datos de pagos:</strong> 6 años, conforme a la legislación mercantil y fiscal.</li>
        </ul>
    </section>

    <section class="legal-section" id="destinatarios">
        <h2><i class="fas fa-share-nodes"></i> 5. Destinatarios y Transferencias</h2>
        <p>Solo el personal autorizado del centro tiene acceso a tus datos. Las transferencias internacionales (a través del token Firebase para notificaciones de Google Firebase) se amparan en las Cláusulas Contractuales Tipo de la UE.</p>
    </section>

    <section class="legal-section" id="derechos">
        <h2><i class="fas fa-hand-holding-heart"></i> 6. Tus Derechos</h2>
        <p>Tienes los derechos de acceso, rectificación, supresión ("derecho al olvido"), limitación del tratamiento, oposición y portabilidad. Puedes ejercerlos enviando un escrito al centro educativo o al e-mail de contacto.</p>
    </section>

    <section class="legal-section" id="menores">
        <h2><i class="fas fa-child"></i> 7. Protección de Menores</h2>
        <p>El tratamiento de datos de menores de 14 años requiere el consentimiento de sus tutores legales.</p>
    </section>

    <section class="legal-section" id="app-movil">
        <h2><i class="fas fa-mobile-screen-button"></i> 8. Aplicación Móvil</h2>
        <p>Al acceder con la app móvil de <?= htmlspecialchars(FeatureGuard::getCenterName()) ?> se trata adicionalmente el token FCM (Firebase Cloud Messaging) para notificaciones push. La app almacena tus credenciales cifradas localmente y no solicita accesos invasivos.</p>
    </section>

    <section class="legal-section" id="seguridad">
        <h2><i class="fas fa-lock"></i> 9. Seguridad de los Datos</h2>
        <p>Aplicamos cifrado avanzado (AES-256) en la base de datos para tu información sensible (DNI, teléfono, dirección, MFA), comunicaciones SSL/TLS y límites de tasa para evitar accesos no autorizados.</p>
    </section>

    <section class="legal-section" id="cambios">
        <h2><i class="fas fa-history"></i> 10. Cambios en esta Política</h2>
        <p>Cualquier modificación de esta política se publicará en esta página y se notificará en la plataforma.</p>
        <p style="color:var(--mut);font-size:.85rem;margin-top:16px;">Última actualización: <?= date('d/m/Y') ?></p>
    </section>

</main>
