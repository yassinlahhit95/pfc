<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Política de Privacidad | <?php require_once __DIR__ . '/../../include/FeatureGuard.php'; echo htmlspecialchars(FeatureGuard::getCenterName()); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <style>
        body { font-family: sans-serif; line-height: 1.6; padding: 40px; background: #f8fafc; }
        .legal-container { max-width: 800px; margin: 0 auto; background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        h1 { color: #252260; margin-bottom: 30px; }
        h2 { color: #4f46e5; font-size: 1.25rem; margin-top: 25px; }
        table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
        th, td { border: 1px solid #e5e7eb; padding: 8px 12px; text-align: left; }
        th { background: #f3f4f6; }
    </style>
</head>
<body>
    <div class="legal-container">
        <h1>Política de Privacidad</h1>
        <p><em>Última actualización: junio de 2026</em></p>
        <p>En <strong><?= htmlspecialchars(FeatureGuard::getCenterName() ?? "Centro Educativo") ?></strong>, nos comprometemos con la protección de sus datos personales conforme al <strong>Reglamento (UE) 2016/679 (RGPD)</strong> y a la <strong>Ley Orgánica 3/2018 (LOPDGDD)</strong>.</p>

        <h2>1. Responsable del Tratamiento</h2>
        <p>El <strong>responsable del tratamiento</strong> es el centro educativo que utiliza la plataforma <?= htmlspecialchars(FeatureGuard::getCenterName() ?? "Centro Educativo") ?>. Para cualquier consulta o ejercicio de derechos, diríjase a la secretaría del centro o a sus canales de contacto oficiales.</p>

        <h2>2. Encargado del Tratamiento</h2>
        <p><strong><?= htmlspecialchars(FeatureGuard::getCenterName() ?? "Centro Educativo") ?></strong> actúa como encargado del tratamiento en nombre del centro educativo. El acceso y uso de los datos se rige por el correspondiente Acuerdo de Encargado del Tratamiento (DPA) suscrito entre el centro y <?= htmlspecialchars(FeatureGuard::getCenterName() ?? "Centro Educativo") ?>.</p>

        <h2>3. Finalidad del Tratamiento</h2>
        <ul>
            <li>Gestión del proceso de pre-matrícula y admisión.</li>
            <li>Formalización del expediente académico en caso de admisión.</li>
            <li>Comunicación con el alumno y su tutor legal sobre el estado de la solicitud.</li>
            <li>Envío de notificaciones académicas (notas, avisos del centro).</li>
        </ul>

        <h2>4. Base Jurídica</h2>
        <p>El tratamiento se basa en la <strong>ejecución de una relación contractual o precontractual</strong> (Art. 6.1.b RGPD) y en el cumplimiento de <strong>obligaciones legales del centro educativo</strong> (Art. 6.1.c RGPD).</p>

        <h2>5. Categoría de Datos Recogidos</h2>
        <ul>
            <li><strong>Datos identificativos:</strong> DNI/NIE, nombre y apellidos.</li>
            <li><strong>Datos de contacto:</strong> correo electrónico, teléfono.</li>
            <li><strong>Datos académicos:</strong> calificaciones, expediente, ciclo formativo.</li>
            <li><strong>Datos de tutores legales</strong> (cuando el alumno es menor de edad): nombre, DNI, correo, teléfono y parentesco.</li>
            <li><strong>Documentos adjuntos:</strong> copia del DNI (anverso/reverso), expediente académico, fotografía.</li>
        </ul>

        <h2>6. Destinatarios y Encargados del Tratamiento</h2>
        <p>Sus datos pueden ser comunicados a los siguientes <strong>subencargados del tratamiento</strong> para prestar el servicio:</p>
        <table>
            <thead><tr><th>Proveedor</th><th>Servicio</th><th>Ubicación</th></tr></thead>
            <tbody>
                <tr><td>Brevo (Sendinblue)</td><td>Envío de correos electrónicos transaccionales</td><td>UE (Francia)</td></tr>
                <tr><td>Google Firebase</td><td>Notificaciones push en tiempo real</td><td>EE.UU. (cubierto por Cláusulas Contractuales Tipo)</td></tr>
            </tbody>
        </table>
        <p>No cedemos sus datos a terceros con fines comerciales ni de marketing.</p>

        <h2>7. Transferencias Internacionales</h2>
        <p>Google Firebase puede implicar transferencias de datos a EE.UU. Dichas transferencias están amparadas por las <strong>Cláusulas Contractuales Tipo (CCT)</strong> aprobadas por la Comisión Europea (Art. 46 RGPD).</p>

        <h2>8. Conservación de los Datos</h2>
        <ul>
            <li><strong>Pre-matrículas rechazadas o no finalizadas:</strong> se eliminarán al finalizar el período de admisión del curso académico correspondiente.</li>
            <li><strong>Expedientes de alumnos admitidos:</strong> se conservarán durante el tiempo que dure la relación académica con el centro y los plazos legales posteriores (normativa educativa aplicable).</li>
            <li><strong>Registros de seguridad (logs):</strong> máximo 30 días, con fines de detección de incidentes.</li>
        </ul>

        <h2>9. Sus Derechos (RGPD Arts. 15–22)</h2>
        <p>Puede ejercer los siguientes derechos ante el centro educativo:</p>
        <ul>
            <li><strong>Acceso (Art. 15):</strong> obtener confirmación de si se tratan sus datos y una copia de los mismos.</li>
            <li><strong>Rectificación (Art. 16):</strong> corregir datos inexactos o incompletos.</li>
            <li><strong>Supresión / "Derecho al olvido" (Art. 17):</strong> solicitar la eliminación de sus datos cuando ya no sean necesarios o retire su consentimiento.</li>
            <li><strong>Limitación del tratamiento (Art. 18):</strong> solicitar la suspensión temporal del tratamiento.</li>
            <li><strong>Portabilidad (Art. 20):</strong> recibir sus datos en formato estructurado y de uso común para transmitirlos a otro responsable.</li>
            <li><strong>Oposición (Art. 21):</strong> oponerse al tratamiento en determinadas circunstancias.</li>
        </ul>
        <p>Para ejercer sus derechos, diríjase por escrito a la secretaría del centro. Su solicitud será atendida en el plazo máximo de <strong>1 mes</strong> (ampliable a 3 meses en casos complejos).</p>
        <p>Si considera que el tratamiento de sus datos no se ajusta a la normativa, puede presentar una reclamación ante la <strong>Agencia Española de Protección de Datos (AEPD)</strong>: <a href="https://www.aepd.es" target="_blank" rel="noopener noreferrer">www.aepd.es</a>.</p>

        <h2>10. Cookies y Tecnologías de Seguimiento</h2>
        <p>Esta plataforma utiliza:</p>
        <ul>
            <li><strong>Cookies de sesión (AULAPROSESSID):</strong> estrictamente necesarias para mantener la sesión autenticada. No requieren consentimiento (Art. 22.2 LSSI).</li>
            <li><strong>Google Fonts:</strong> la página de inicio carga fuentes desde los servidores de Google, lo que puede registrar su dirección IP en Google. Puede consultar la política de privacidad de Google en <a href="https://policies.google.com/privacy" target="_blank" rel="noopener noreferrer">policies.google.com/privacy</a>.</li>
            <li><strong>Firebase Cloud Messaging:</strong> si acepta las notificaciones push, se genera un token de dispositivo para su envío. Este token no identifica a la persona y puede eliminarse desde la configuración del navegador.</li>
        </ul>
        <p>No se utilizan cookies de publicidad ni de rastreo de terceros.</p>

        <h2>11. Seguridad de los Datos</h2>
        <p><?= htmlspecialchars(FeatureGuard::getCenterName() ?? "Centro Educativo") ?> aplica medidas técnicas y organizativas adecuadas para garantizar la seguridad de los datos, incluyendo: cifrado de contraseñas con bcrypt (coste 12), protección CSRF, control de acceso por roles, limitación de intentos de inicio de sesión, registros de auditoría y comunicaciones cifradas por TLS.</p>
        <p>En caso de violación de seguridad que afecte a sus datos, el centro educativo notificará a la AEPD en un plazo máximo de <strong>72 horas</strong> (Art. 33 RGPD) y, si el riesgo es elevado, a los afectados sin dilación indebida (Art. 34 RGPD).</p>

        <div class="mt-5 pt-3 border-top text-center">
            <a href="javascript:window.close()" class="btn btn-secondary">Cerrar Ventana</a>
            <a href="/" class="btn btn-primary ms-2">Volver al Inicio</a>
        </div>
    </div>
</body>
</html>
