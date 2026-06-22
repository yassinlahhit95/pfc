<?php
$legal_titulo = 'Política de Privacidad';
$legal_pagina = 'privacidad';
require __DIR__ . '/_header.php';
$hoy = date('d/m/Y');
?>

<div class="legal-hero">
    <h1><i class="fas fa-user-shield" style="margin-right:10px;opacity:.9;"></i>Política de Privacidad</h1>
    <span class="badge">RGPD · LOPD-GDD · Art. 13 y 14</span>
</div>

<main class="legal-main">

    <div class="legal-toc">
        <h2><i class="fas fa-list-ul" style="margin-right:6px;"></i> Contenido</h2>
        <ol>
            <li><a href="#responsable">Responsable del tratamiento</a></li>
            <li><a href="#datos">Datos que tratamos</a></li>
            <li><a href="#finalidades">Finalidades y base jurídica</a></li>
            <li><a href="#conservacion">Conservación de datos</a></li>
            <li><a href="#destinatarios">Destinatarios y transferencias</a></li>
            <li><a href="#derechos">Tus derechos</a></li>
            <li><a href="#menores">Protección de menores</a></li>
            <li><a href="#seguridad">Seguridad</a></li>
            <li><a href="#cambios">Cambios en esta política</a></li>
        </ol>
    </div>

    <div class="legal-info-box verde">
        <i class="fas fa-check-circle"></i>
        <span>Nos comprometemos a tratar tus datos personales con total transparencia, en estricto cumplimiento del <strong>Reglamento (UE) 2016/679 (RGPD)</strong> y la <strong>Ley Orgánica 3/2018 (LOPD-GDD)</strong>.</span>
    </div>

    <section class="legal-section" id="responsable">
        <h2><i class="fas fa-building"></i> 1. Responsable del Tratamiento</h2>
        <ul>
            <li><strong>Identidad:</strong> <?= htmlspecialchars($nombreCentro) ?></li>
            <li><strong>NIF/CIF:</strong> [Introduce aquí el NIF o CIF del centro]</li>
            <li><strong>Dirección postal:</strong> <?= htmlspecialchars(trim(($cfg['direccionCentro'] ?? '') . ', ' . ($cfg['ciudadCentro'] ?? '') . ' ' . ($cfg['cpCentro'] ?? ''))) ?: '[Dirección del centro]' ?></li>
            <li><strong>Teléfono:</strong> <?= !empty($cfg['telefonoCentro']) ? htmlspecialchars($cfg['telefonoCentro']) : '[Teléfono]' ?></li>
            <li><strong>Contacto:</strong> <?= !empty($emailCentro) ? '<a href="mailto:' . htmlspecialchars($emailCentro) . '">' . htmlspecialchars($emailCentro) . '</a>' : '[email del centro]' ?></li>
        </ul>
        <p style="margin-top:12px;"><strong>Proveedor tecnológico (Encargado del tratamiento):</strong> AulaPro SaaS, responsable del sistema de gestión escolar, actúa como encargado del tratamiento bajo contrato de confidencialidad con el centro, en los términos del Art. 28 RGPD.</p>
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
            <li>Nombre completo, email, contraseña (hash), fecha de nacimiento, NIA/DNI (identificador académico).</li>
            <li>Ciclo formativo y curso asignado.</li>
            <li>Calificaciones, notas de módulos y proyectos fin de grado (TFG).</li>
            <li>Historial de pagos de matrícula (si el módulo está habilitado).</li>
            <li>Retos académicos y asistencia (si los módulos están habilitados).</li>
        </ul>

        <h3>Tutores legales</h3>
        <ul>
            <li>Nombre completo, correo electrónico, contraseña (hash), relación con el estudiante.</li>
            <li>Acceso a la información académica del estudiante tutelado.</li>
        </ul>

        <h3>Solicitudes de admisión</h3>
        <ul>
            <li>Datos del solicitante: nombre, email, teléfono, ciclo de interés y datos adicionales indicados en el formulario de pre-matrícula.</li>
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
                <tr><td style="padding:10px 14px;border-bottom:1px solid var(--border);">Gestión de solicitudes de admisión</td><td style="padding:10px 14px;border-bottom:1px solid var(--border);">Consentimiento del interesado</td></tr>
                <tr><td style="padding:10px 14px;border-bottom:1px solid var(--border);">Seguridad y control de acceso (logs de actividad)</td><td style="padding:10px 14px;border-bottom:1px solid var(--border);">Interés legítimo / obligación legal</td></tr>
                <tr><td style="padding:10px 14px;">Envío de notificaciones por email (calificaciones, eventos)</td><td style="padding:10px 14px;">Interés legítimo / consentimiento</td></tr>
            </tbody>
        </table>
    </section>

    <section class="legal-section" id="conservacion">
        <h2><i class="fas fa-clock"></i> 4. Conservación de Datos</h2>
        <p>Los datos se conservarán durante el tiempo necesario para la finalidad para la que fueron recogidos y, en todo caso, durante los plazos exigidos por la normativa aplicable:</p>
        <ul>
            <li><strong>Datos académicos:</strong> Mínimo 5 años desde la finalización de los estudios, conforme a la normativa educativa aplicable.</li>
            <li><strong>Datos de acceso y logs:</strong> Máximo 12 meses, salvo requerimiento judicial.</li>
            <li><strong>Solicitudes de admisión no aceptadas:</strong> 12 meses desde la solicitud.</li>
            <li><strong>Mensajes internos:</strong> Mientras el usuario tenga cuenta activa, y 6 meses tras la baja.</li>
            <li><strong>Datos de pagos:</strong> 6 años, conforme a la legislación mercantil y fiscal.</li>
        </ul>
        <p>Transcurridos estos plazos, los datos serán eliminados o anonimizados de forma segura.</p>
    </section>

    <section class="legal-section" id="destinatarios">
        <h2><i class="fas fa-share-nodes"></i> 5. Destinatarios y Transferencias Internacionales</h2>
        <h3>Destinatarios internos</h3>
        <p>Solo el personal autorizado del centro (dirección, docentes asignados) tiene acceso a los datos estrictamente necesarios para el desempeño de sus funciones.</p>
        <h3>Proveedores de servicio (encargados del tratamiento)</h3>
        <ul>
            <li><strong>Proveedor de hosting:</strong> Servidor de alojamiento web con sede en la UE.</li>
            <li><strong>Firebase (Google LLC):</strong> Utilizado para notificaciones push en tiempo real. Google actúa como encargado del tratamiento conforme a las Cláusulas Contractuales Tipo de la UE.</li>
            <li><strong>Servicio de correo electrónico:</strong> Para el envío de notificaciones transaccionales.</li>
        </ul>
        <h3>Transferencias internacionales</h3>
        <p>Google LLC (Firebase) puede realizar transferencias de datos fuera del Espacio Económico Europeo, amparadas en las Cláusulas Contractuales Tipo aprobadas por la Comisión Europea (Art. 46 RGPD). No se realizan otras transferencias internacionales de datos.</p>
        <h3>Autoridades competentes</h3>
        <p>Los datos podrán comunicarse a autoridades educativas, fiscales o judiciales cuando así lo exija la normativa vigente.</p>
    </section>

    <section class="legal-section" id="derechos">
        <h2><i class="fas fa-hand-holding-heart"></i> 6. Tus Derechos</h2>
        <p>En virtud del RGPD y la LOPD-GDD, puedes ejercer los siguientes derechos enviando un escrito a <strong><?= !empty($emailCentro) ? htmlspecialchars($emailCentro) : '[email del centro]' ?></strong> adjuntando copia de tu DNI o documento identificativo equivalente:</p>

        <div class="derechos-grid">
            <div class="derecho-card">
                <i class="fas fa-eye"></i>
                <strong>Acceso</strong>
                <span>Obtener confirmación sobre si tratamos tus datos y acceder a ellos.</span>
            </div>
            <div class="derecho-card">
                <i class="fas fa-pencil"></i>
                <strong>Rectificación</strong>
                <span>Corregir datos inexactos o incompletos.</span>
            </div>
            <div class="derecho-card">
                <i class="fas fa-trash"></i>
                <strong>Supresión</strong>
                <span>Solicitar la eliminación de tus datos («derecho al olvido»).</span>
            </div>
            <div class="derecho-card">
                <i class="fas fa-pause-circle"></i>
                <strong>Limitación</strong>
                <span>Restringir el tratamiento de tus datos en determinadas circunstancias.</span>
            </div>
            <div class="derecho-card">
                <i class="fas fa-ban"></i>
                <strong>Oposición</strong>
                <span>Oponerte al tratamiento basado en interés legítimo.</span>
            </div>
            <div class="derecho-card">
                <i class="fas fa-file-export"></i>
                <strong>Portabilidad</strong>
                <span>Recibir tus datos en formato estructurado y legible por máquina.</span>
            </div>
        </div>

        <p>Asimismo, si el tratamiento se basa en tu consentimiento, tienes derecho a <strong>retirarlo en cualquier momento</strong> sin que ello afecte a la licitud del tratamiento previo.</p>
        <p>Si consideras que el tratamiento de tus datos vulnera la normativa, puedes presentar una reclamación ante la <strong>Agencia Española de Protección de Datos (AEPD)</strong>: <a href="https://www.aepd.es" target="_blank" rel="noopener">www.aepd.es</a>.</p>
    </section>

    <section class="legal-section" id="menores">
        <h2><i class="fas fa-child"></i> 7. Protección de Menores</h2>
        <p>Conforme al Art. 8 RGPD y el Art. 7 LOPD-GDD, el tratamiento de datos de menores de 14 años requiere el <strong>consentimiento del padre, madre o tutor legal</strong>.</p>
        <p>Los datos de estudiantes menores se incorporan a la plataforma por el centro educativo, que actúa como responsable del tratamiento conforme a la normativa educativa vigente. Los padres y tutores legales tienen acceso a los datos académicos de sus hijos a través del portal de tutores.</p>
    </section>

    <section class="legal-section" id="seguridad">
        <h2><i class="fas fa-lock"></i> 8. Seguridad de los Datos</h2>
        <p>Aplicamos medidas técnicas y organizativas apropiadas para garantizar la seguridad de tus datos personales (Art. 32 RGPD), entre otras:</p>
        <ul>
            <li>Cifrado de contraseñas mediante algoritmos seguros (bcrypt).</li>
            <li>Comunicaciones cifradas mediante protocolo HTTPS/TLS.</li>
            <li>Control de acceso basado en roles (administrador, docente, estudiante, tutor).</li>
            <li>Autenticación en dos factores (2FA) disponible para administradores.</li>
            <li>Registros de auditoría de acceso y actividad.</li>
            <li>Limitación de intentos de acceso para prevenir ataques de fuerza bruta.</li>
            <li>Copias de seguridad periódicas de la base de datos.</li>
        </ul>
        <p>En caso de brecha de seguridad que pueda afectar a tus derechos y libertades, te lo notificaremos conforme al Art. 34 RGPD.</p>
    </section>

    <section class="legal-section" id="cambios">
        <h2><i class="fas fa-history"></i> 9. Cambios en esta Política</h2>
        <p>Podemos actualizar esta política de privacidad para adaptarla a cambios legislativos o en el funcionamiento de la plataforma. Las modificaciones significativas se comunicarán mediante aviso en la plataforma o por correo electrónico.</p>
        <p style="color:var(--mut);font-size:.85rem;margin-top:16px;">Última actualización: <?= $hoy ?></p>
    </section>

</main>

<?php require __DIR__ . '/_footer.php'; ?>
