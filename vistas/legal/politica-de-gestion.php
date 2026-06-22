<?php
$legal_titulo = 'Política de Gestión';
$legal_pagina = 'gestion';
require __DIR__ . '/_header.php';
$hoy = date('d/m/Y');
?>

<div class="legal-hero">
    <h1><i class="fas fa-clipboard-check" style="margin-right:10px;opacity:.9;"></i>Política de Gestión</h1>
    <span class="badge">Calidad del Servicio · Seguridad · Continuidad</span>
</div>

<main class="legal-main">

    <div class="legal-toc">
        <h2><i class="fas fa-list-ul" style="margin-right:6px;"></i> Contenido</h2>
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
        <p>La presente <strong>Política de Gestión</strong> describe los compromisos de calidad, seguridad y continuidad de la plataforma <strong>AulaPro</strong> — sistema de gestión escolar proporcionado a centros educativos de formación profesional.</p>
        <p>Esta política aplica a:</p>
        <ul>
            <li>La plataforma web AulaPro en su totalidad (panel de administración, portal docente, portal del estudiante y portal de tutores).</li>
            <li>Las APIs e integraciones asociadas (Firebase, servicio de correo electrónico).</li>
            <li>La infraestructura de hosting que soporta el servicio.</li>
        </ul>
        <p>Los centros educativos que utilizan AulaPro se comprometen a cumplir con esta política y a exigir su cumplimiento a sus usuarios.</p>
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
                <span class="lbl">Tiempo máx. de respuesta a incidencia crítica</span>
            </div>
            <div class="sla-card">
                <span class="val">24h</span>
                <span class="lbl">Ventana de mantenimiento programado</span>
            </div>
            <div class="sla-card">
                <span class="val">Diaria</span>
                <span class="lbl">Frecuencia de copias de seguridad</span>
            </div>
        </div>

        <h3>Exclusiones de SLA</h3>
        <p>El compromiso de disponibilidad no incluye:</p>
        <ul>
            <li>Interrupciones por mantenimiento programado con notificación previa de al menos 24 horas.</li>
            <li>Interrupciones causadas por factores fuera del control razonable del proveedor (fuerza mayor, fallos del proveedor de hosting, ataques DDoS masivos).</li>
            <li>Interrupciones debidas a configuraciones incorrectas realizadas por el propio centro.</li>
        </ul>

        <h3>Notificación de interrupciones</h3>
        <p>Cualquier interrupción planificada o no planificada que afecte al servicio se comunicará al centro educativo mediante correo electrónico a la dirección de contacto registrada, con la mayor antelación posible.</p>
    </section>

    <section class="legal-section" id="seguridad">
        <h2><i class="fas fa-shield-alt"></i> 3. Seguridad de la Información</h2>
        <p>AulaPro implementa un conjunto de medidas de seguridad conforme al <strong>Art. 32 del RGPD</strong> y las mejores prácticas del sector (OWASP, ENS):</p>

        <h3>Control de acceso</h3>
        <ul>
            <li>Autenticación mediante usuario y contraseña cifrada con <strong>bcrypt</strong> (coste ≥ 12).</li>
            <li>Autenticación en dos factores (2FA/TOTP) disponible para administradores.</li>
            <li>Control de acceso basado en roles: administrador, docente, estudiante, tutor. Cada rol solo accede a los datos que necesita.</li>
            <li>Bloqueo automático tras múltiples intentos de acceso fallidos.</li>
            <li>Sesiones con cookie <code>SameSite=Lax; Secure; HttpOnly</code> y tiempo de expiración.</li>
        </ul>

        <h3>Transmisión de datos</h3>
        <ul>
            <li>Todas las comunicaciones se realizan bajo protocolo <strong>HTTPS/TLS 1.2+</strong>.</li>
            <li>Cabeceras de seguridad HTTP: <code>X-Frame-Options</code>, <code>X-Content-Type-Options</code>, <code>Content-Security-Policy</code>, <code>Referrer-Policy</code>.</li>
        </ul>

        <h3>Protección de la aplicación</h3>
        <ul>
            <li>Protección contra inyección SQL mediante consultas preparadas (<em>prepared statements</em>) en todas las operaciones de base de datos.</li>
            <li>Protección XSS con escapado sistemático de salida.</li>
            <li>Tokens CSRF en todos los formularios y peticiones de modificación.</li>
            <li>Validación y sanitización de todas las entradas de usuario.</li>
            <li>Limitación de tasa de peticiones (<em>rate limiting</em>) para prevenir ataques de fuerza bruta y abuso de API.</li>
            <li>Protección contra la subida de archivos maliciosos (validación de tipo MIME, extensión y tamaño).</li>
        </ul>

        <h3>Auditoría</h3>
        <ul>
            <li>Registro de acciones críticas: modificaciones de datos académicos, cambios de configuración, accesos a datos sensibles.</li>
            <li>Logs de acceso al sistema conservados durante 12 meses.</li>
        </ul>
    </section>

    <section class="legal-section" id="datos">
        <h2><i class="fas fa-hard-drive"></i> 4. Gestión de Datos y Copias de Seguridad</h2>
        <h3>Copias de seguridad</h3>
        <ul>
            <li>Copias de seguridad de la base de datos realizadas con frecuencia <strong>diaria</strong>.</li>
            <li>Retención de copias durante un mínimo de <strong>30 días</strong>.</li>
            <li>Almacenamiento de copias en ubicación separada del servidor principal.</li>
        </ul>
        <h3>Integridad de los datos</h3>
        <ul>
            <li>Las transacciones de base de datos se gestionan con mecanismos que garantizan la integridad referencial.</li>
            <li>Los archivos subidos por los usuarios (TFG, logotipos) se almacenan en directorio protegido fuera del document root accesible públicamente.</li>
        </ul>
        <h3>Eliminación segura</h3>
        <p>Cuando se solicita la eliminación de datos personales o al finalizar la relación con el centro, los datos son eliminados de forma segura e irreversible conforme a los plazos indicados en la <a href="/vistas/legal/politica-de-privacidad.php">Política de Privacidad</a>.</p>
    </section>

    <section class="legal-section" id="incidencias">
        <h2><i class="fas fa-triangle-exclamation"></i> 5. Gestión de Incidencias</h2>
        <h3>Clasificación</h3>
        <ul>
            <li><strong>Crítica (P1):</strong> Plataforma inaccesible o pérdida de datos. Respuesta: &lt; 4 horas.</li>
            <li><strong>Alta (P2):</strong> Funcionalidad principal afectada. Respuesta: &lt; 8 horas laborables.</li>
            <li><strong>Media (P3):</strong> Funcionalidad secundaria degradada. Respuesta: &lt; 24 horas laborables.</li>
            <li><strong>Baja (P4):</strong> Problema menor o cosmético. Respuesta: próximo ciclo de actualización.</li>
        </ul>
        <h3>Canal de reporte</h3>
        <p>Las incidencias se reportan al correo electrónico de contacto del proveedor: <?= !empty($emailCentro) ? '<a href="mailto:' . htmlspecialchars($emailCentro) . '">' . htmlspecialchars($emailCentro) . '</a>' : '[email de soporte AulaPro]' ?>. Incluye en tu mensaje: descripción del problema, usuario afectado, captura de pantalla si es posible.</p>
        <h3>Brechas de seguridad</h3>
        <p>En caso de brecha de seguridad que afecte a datos personales, el proveedor lo notificará al centro educativo en un plazo máximo de <strong>72 horas</strong> desde la detección, conforme al Art. 33 RGPD, con la información necesaria para que el centro evalúe la obligación de notificación a la AEPD.</p>
    </section>

    <section class="legal-section" id="actualizaciones">
        <h2><i class="fas fa-rotate"></i> 6. Actualizaciones y Mantenimiento</h2>
        <h3>Ciclo de actualizaciones</h3>
        <ul>
            <li><strong>Parches de seguridad:</strong> Publicación en 24–48 horas tras la detección del fallo.</li>
            <li><strong>Actualizaciones funcionales:</strong> Comunicadas con al menos 48 horas de antelación.</li>
            <li><strong>Actualizaciones mayores:</strong> Comunicadas con al menos 7 días de antelación, preferiblemente en fin de semana o periodo no lectivo.</li>
        </ul>
        <h3>Ventanas de mantenimiento</h3>
        <p>El mantenimiento programado se realizará preferiblemente en horario de menor actividad (madrugada o fines de semana), minimizando el impacto en el uso de la plataforma.</p>
    </section>

    <section class="legal-section" id="responsabilidades">
        <h2><i class="fas fa-handshake"></i> 7. Responsabilidades</h2>
        <h3>Del proveedor (AulaPro)</h3>
        <ul>
            <li>Mantener la plataforma disponible y actualizada conforme a los compromisos de este documento.</li>
            <li>Proteger los datos personales tratados en el sistema.</li>
            <li>Notificar incidencias y brechas de seguridad en los plazos establecidos.</li>
            <li>Actuar como encargado del tratamiento bajo las instrucciones del responsable (el centro educativo).</li>
        </ul>
        <h3>Del centro educativo</h3>
        <ul>
            <li>Gestionar los accesos de los usuarios de forma responsable (altas, bajas, cambios de rol).</li>
            <li>Informar a los usuarios sobre el tratamiento de sus datos conforme a esta política.</li>
            <li>Reportar cualquier incidencia o uso indebido detectado al proveedor.</li>
            <li>Mantener actualizada la información de contacto para comunicaciones del proveedor.</li>
            <li>No compartir credenciales de administrador ni permitir accesos no autorizados.</li>
        </ul>
    </section>

    <section class="legal-section" id="mejora">
        <h2><i class="fas fa-chart-line"></i> 8. Mejora Continua</h2>
        <p>AulaPro se compromete con la <strong>mejora continua</strong> de la plataforma mediante:</p>
        <ul>
            <li>Revisiones periódicas de seguridad (auditorías internas y externas).</li>
            <li>Recopilación de feedback de centros educativos usuarios para priorizar mejoras.</li>
            <li>Actualización de la política de gestión al menos una vez al año o cuando cambien las circunstancias relevantes.</li>
            <li>Formación continua del equipo técnico en buenas prácticas de desarrollo seguro.</li>
        </ul>
        <div class="legal-info-box verde">
            <i class="fas fa-envelope"></i>
            <span>¿Tienes sugerencias de mejora o has detectado un problema de seguridad? Escríbenos a <strong><?= !empty($emailCentro) ? htmlspecialchars($emailCentro) : '[email de contacto]' ?></strong>. Tu aportación nos ayuda a mejorar el servicio para todos los centros.</span>
        </div>
        <p style="color:var(--mut);font-size:.85rem;margin-top:16px;">Última actualización: <?= $hoy ?></p>
    </section>

</main>

<?php require __DIR__ . '/_footer.php'; ?>
