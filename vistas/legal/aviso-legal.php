<?php
$legal_titulo = 'Aviso Legal';
$legal_pagina = 'aviso-legal';
require __DIR__ . '/_header.php';
// $cfg, $nombreCentro, $emailCentro are available from _header.php
$hoy = date('d/m/Y');
?>

<div class="legal-hero">
    <h1><i class="fas fa-scale-balanced" style="margin-right:10px;opacity:.9;"></i>Aviso Legal</h1>
    <span class="badge">LSSI-CE · Art. 10</span>
</div>

<main class="legal-main">

    <div class="legal-toc">
        <h2><i class="fas fa-list-ul" style="margin-right:6px;"></i> Contenido</h2>
        <ol>
            <li><a href="#titular">Identificación del titular</a></li>
            <li><a href="#uso">Condiciones de uso</a></li>
            <li><a href="#propiedad">Propiedad intelectual</a></li>
            <li><a href="#responsabilidad">Responsabilidad</a></li>
            <li><a href="#enlaces">Política de enlaces</a></li>
            <li><a href="#ley">Ley aplicable</a></li>
        </ol>
    </div>

    <section class="legal-section" id="titular">
        <h2><i class="fas fa-building"></i> 1. Identificación del Titular</h2>
        <p>En cumplimiento del artículo 10 de la <strong>Ley 34/2002, de 11 de julio, de Servicios de la Sociedad de la Información y del Comercio Electrónico (LSSI-CE)</strong>, se informa de los siguientes datos:</p>
        <ul>
            <li><strong>Denominación:</strong> <?= htmlspecialchars($nombreCentro) ?></li>
            <li><strong>NIF/CIF:</strong> [Introduce aquí el NIF o CIF del centro]</li>
            <li><strong>Domicilio:</strong> <?= htmlspecialchars(trim(($cfg['direccionCentro'] ?? '') . ' ' . ($cfg['ciudadCentro'] ?? '') . ' ' . ($cfg['cpCentro'] ?? ''))) ?: '[Introduce aquí la dirección del centro]' ?></li>
            <li><strong>Teléfono:</strong> <?= !empty($cfg['telefonoCentro']) ? htmlspecialchars($cfg['telefonoCentro']) : '[Teléfono del centro]' ?></li>
            <li><strong>Correo electrónico:</strong> <?= !empty($emailCentro) ? '<a href="mailto:' . htmlspecialchars($emailCentro) . '">' . htmlspecialchars($emailCentro) . '</a>' : '[Email del centro]' ?></li>
            <li><strong>Actividad principal:</strong> Centro de formación profesional y gestión educativa.</li>
            <li><strong>Plataforma tecnológica:</strong> AulaPro — sistema de gestión escolar desarrollado por AulaPro SaaS.</li>
        </ul>
    </section>

    <section class="legal-section" id="uso">
        <h2><i class="fas fa-laptop"></i> 2. Condiciones de Uso</h2>
        <p>El acceso y uso de esta plataforma atribuye la condición de <strong>usuario</strong> (en adelante, «el Usuario») e implica la aceptación plena y sin reservas de las presentes condiciones de uso.</p>
        <h3>Acceso restringido</h3>
        <p>Esta plataforma está <strong>destinada exclusivamente a usuarios autorizados</strong> del centro: administradores, docentes, estudiantes y tutores legales, según los accesos habilitados por la dirección del centro. El acceso no autorizado está prohibido y podrá ser objeto de las acciones legales correspondientes.</p>
        <h3>Uso correcto</h3>
        <p>El Usuario se compromete a:</p>
        <ul>
            <li>No introducir datos falsos o de terceras personas sin su consentimiento.</li>
            <li>No realizar actividades que dañen, interrumpan o sobrecarguen la plataforma.</li>
            <li>Mantener la confidencialidad de sus credenciales de acceso.</li>
            <li>No utilizar la plataforma para fines ilícitos o contrarios a la moral y al orden público.</li>
        </ul>
        <h3>Modificaciones</h3>
        <p>El centro se reserva el derecho a modificar estas condiciones en cualquier momento, publicando la versión actualizada en esta misma página. El uso continuado de la plataforma implica la aceptación de las condiciones vigentes.</p>
    </section>

    <section class="legal-section" id="propiedad">
        <h2><i class="fas fa-copyright"></i> 3. Propiedad Intelectual e Industrial</h2>
        <p>Todos los elementos de esta plataforma — incluyendo, pero no limitado a, software, código fuente, diseño, marca, logotipos, textos e imágenes — están protegidos por las leyes de <strong>propiedad intelectual e industrial</strong> españolas y de la Unión Europea.</p>
        <p>Queda expresamente prohibida la reproducción, distribución, transformación o comunicación pública de cualquier elemento de la plataforma sin la autorización expresa y por escrito del titular.</p>
        <p>Los logotipos del centro educativo y del Gobierno/Administración son propiedad de sus respectivos titulares y se muestran únicamente con carácter identificativo.</p>
    </section>

    <section class="legal-section" id="responsabilidad">
        <h2><i class="fas fa-shield-halved"></i> 4. Responsabilidad y Garantías</h2>
        <h3>Disponibilidad del servicio</h3>
        <p>El centro no garantiza la disponibilidad ininterrumpida de la plataforma, que puede quedar temporalmente suspendida por razones de mantenimiento técnico, mejoras o causas ajenas al control del titular.</p>
        <h3>Exactitud de la información</h3>
        <p>El titular procura que la información contenida sea precisa y esté actualizada, pero no garantiza la ausencia de errores. Los datos académicos (calificaciones, horarios, etc.) tienen carácter informativo y el original en papel o sistema oficial del centro prevalece ante cualquier discrepancia.</p>
        <h3>Contenido generado por usuarios</h3>
        <p>El centro no se hace responsable de los contenidos introducidos por los usuarios de la plataforma (mensajes, archivos adjuntos, etc.), aunque se reserva el derecho de eliminar aquellos que infrinjan la legalidad o las normas de uso.</p>
        <div class="legal-info-box">
            <i class="fas fa-info-circle"></i>
            <span>En caso de detectar contenido inapropiado o uso indebido de la plataforma, puede notificarlo al correo: <strong><?= !empty($emailCentro) ? htmlspecialchars($emailCentro) : '[email del centro]' ?></strong></span>
        </div>
    </section>

    <section class="legal-section" id="enlaces">
        <h2><i class="fas fa-link"></i> 5. Política de Enlaces</h2>
        <p>La plataforma puede contener enlaces a sitios web de terceros (por ejemplo, plataformas educativas externas, portales de la Administración). Estos enlaces se proporcionan exclusivamente como referencia y el titular no controla ni asume responsabilidad por los contenidos, privacidad o disponibilidad de dichos sitios.</p>
        <p>Si deseas enlazar a esta plataforma desde un sitio externo, debes solicitar autorización expresa al titular.</p>
    </section>

    <section class="legal-section" id="ley">
        <h2><i class="fas fa-gavel"></i> 6. Ley Aplicable y Jurisdicción</h2>
        <p>Las presentes condiciones de uso se rigen por la <strong>legislación española</strong>, en particular por:</p>
        <ul>
            <li>Ley 34/2002, de 11 de julio, de Servicios de la Sociedad de la Información (LSSI-CE).</li>
            <li>Ley Orgánica 3/2018, de 5 de diciembre, de Protección de Datos Personales y Garantía de los Derechos Digitales (LOPD-GDD).</li>
            <li>Reglamento (UE) 2016/679 del Parlamento Europeo (RGPD).</li>
            <li>Real Decreto Legislativo 1/1996, Ley de Propiedad Intelectual.</li>
        </ul>
        <p>Para la resolución de cualquier conflicto derivado del uso de esta plataforma, las partes se someten a los <strong>Juzgados y Tribunales del domicilio del titular</strong>, con renuncia expresa a cualquier otro fuero que pudiera corresponderles.</p>
        <p style="color:var(--mut);font-size:.85rem;margin-top:16px;">Última actualización: <?= $hoy ?></p>
    </section>

</main>

<?php require __DIR__ . '/_footer.php'; ?>
