<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../include/ProfesorGuard.php';

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../vistas/login.php");
    exit;
}

require_once __DIR__ . "/../../modelos/aula.php";
require_once __DIR__ . "/../../modelos/estudiantes.php";
require_once __DIR__ . "/../../modelos/profesores.php";
require_once __DIR__ . "/../../modelos/modulos.php";
require_once __DIR__ . "/../comunes/email_helper.php";
require_once __DIR__ . "/../../include/Logger.php";

// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN Y VALIDACIÓN
// ══════════════════════════════════════════════════════════════════════
$idProfesor = $_SESSION['idProfesor'];
$idSesion = (int)($_GET['id'] ?? 0);

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
if ($isAjax) { header('Content-Type: application/json'); }

if (!Security::validateCSRFToken(null, false)) {
    if ($isAjax) { echo json_encode(['ok'=>false,'msg'=>'Solicitud inválida']); exit; }
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../vistas/profesores/aula/sesiones.php");
    exit;
}

if (!$idSesion) {
    if ($isAjax) { echo json_encode(['ok'=>false,'msg'=>'ID de sesión no válido']); exit; }
    $_SESSION['errores'] = 'ID de sesión no válido.';
    header("Location: ../../vistas/profesores/aula/sesiones.php");
    exit;
}

$sesion = obtenerSesionPorId($idSesion);

if (!$sesion || $sesion['idProfesor'] != $idProfesor) {
    if ($isAjax) { echo json_encode(['ok'=>false,'msg'=>'No tienes permiso']); exit; }
    $_SESSION['errores'] = 'No tienes permiso para enviar esta sesión.';
    Logger::warning('Intento no autorizado de enviar sesión', ['profesor' => $idProfesor, 'sesion' => $idSesion]);
    header("Location: ../../vistas/profesores/aula/sesiones.php");
    exit;
}

$modulo = obtenerModuloPorId($sesion['idModulo']);
if (!$modulo) {
    if ($isAjax) { echo json_encode(['ok'=>false,'msg'=>'No se encontró el módulo']); exit; }
    $_SESSION['errores'] = 'No se encontró el módulo asociado.';
    Logger::error('Módulo no encontrado', ['modulo' => $sesion['idModulo']]);
    header("Location: ../../vistas/profesores/aula/sesiones.php");
    exit;
}

$idCiclo = $modulo['idCiclo'];
$estudiantes = listarEstudiantesPorCiclo($idCiclo);

if (empty($estudiantes)) {
    if ($isAjax) { echo json_encode(['ok'=>false,'msg'=>'No hay estudiantes en este ciclo']); exit; }
    $_SESSION['errores'] = 'No hay estudiantes en este ciclo.';
    header("Location: ../../vistas/profesores/aula/sesiones.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO — CONSTRUCCIÓN DEL EMAIL
// ══════════════════════════════════════════════════════════════════════
$fechaFormato = date('d/m/Y H:i', strtotime($sesion['fechaSesion'] . ' ' . $sesion['horaSesion']));
$enlace = $sesion['enlaceReunion'];
$titulo = htmlspecialchars($sesion['titulo']);
$descripcion = htmlspecialchars($sesion['descripcion'] ?? '');
$plataforma = ucfirst($sesion['plataforma']);

$htmlContent = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 8px 8px 0 0; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { background: #f9f9f9; padding: 20px; border-bottom: 1px solid #eee; }
        .info-item { background: white; padding: 12px; margin: 10px 0; border-left: 4px solid #667eea; }
        .info-item strong { color: #667eea; }
        .button { display: inline-block; background: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; font-weight: bold; }
        .footer { background: #f0f0f0; padding: 15px; text-align: center; font-size: 12px; color: #666; border-radius: 0 0 8px 8px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎥 Nueva Sesión en Vivo</h1>
        </div>
        <div class="content">
            <p>¡Hola!</p>
            <p>Tu profesor ha programado una nueva sesión en vivo. Aquí están los detalles:</p>

            <div class="info-item">
                <strong>📌 Sesión:</strong><br>
                $titulo
            </div>

            <div class="info-item">
                <strong>📅 Fecha y Hora:</strong><br>
                $fechaFormato
            </div>

            <div class="info-item">
                <strong>🌐 Plataforma:</strong><br>
                $plataforma
            </div>

HTML;

if (!empty($descripcion)) {
    $htmlContent .= <<<HTML
            <div class="info-item">
                <strong>📝 Descripción:</strong><br>
                $descripcion
            </div>
HTML;
}

$htmlContent .= <<<HTML
            <center>
                <a href="$enlace" class="button" target="_blank">Acceder a la Sesión</a>
            </center>

            <p style="text-align: center; color: #666; font-size: 12px;">
                O copia este enlace en tu navegador:<br>
                <code style="background: #f0f0f0; padding: 5px; border-radius: 3px;">$enlace</code>
            </p>
        </div>
        <div class="footer">
            <p>&copy; CFP - AulaPro | Gestión Académica Digital</p>
        </div>
    </div>
</body>
</html>
HTML;

// ══════════════════════════════════════════════════════════════════════
// ENVÍO DE EMAILS
// ══════════════════════════════════════════════════════════════════════
$enviados = 0;
$errores = [];
$emailsInvalidos = [];

foreach ($estudiantes as $estudiante) {
    $email = trim($estudiante['email'] ?? '');
    $nombre = htmlspecialchars($estudiante['nombre'] ?? 'Estudiante');

    if (empty($email)) {
        $emailsInvalidos[] = $nombre;
        continue;
    }

    if (sendEmail($email, "Sesión en Vivo: $titulo", $htmlContent)) {
        $enviados++;
    } else {
        $errores[] = $nombre;
    }
}

Logger::activity('SESION_ENVIADA', $idProfesor, [
    'idSesion' => $idSesion,
    'titulo' => $sesion['titulo'],
    'enviados' => $enviados,
    'total' => count($estudiantes),
    'ciclo' => $idCiclo
]);

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if ($enviados > 0) {
    $msg = "✅ Sesión enviada a $enviados estudiante" . ($enviados != 1 ? 's' : '');
    if (!empty($errores)) {
        $msg .= " ⚠️ Error con: " . implode(', ', $errores);
    }
    if (!empty($emailsInvalidos)) {
        $msg .= " ℹ️ Sin email: " . implode(', ', $emailsInvalidos);
    }
    
    if ($isAjax) { echo json_encode(['ok'=>true,'msg'=>$msg]); exit; }
    $_SESSION['exito'] = $msg;
} else {
    if (!empty($emailsInvalidos)) {
        $msg = 'Los estudiantes de este ciclo no tienen email registrado.';
    } else {
        $msg = 'Error al enviar los correos. Inténtalo de nuevo o contacta con soporte.';
    }
    if ($isAjax) { echo json_encode(['ok'=>false,'msg'=>$msg]); exit; }
    $_SESSION['errores'] = $msg;
}

header("Location: ../../vistas/profesores/aula/sesiones.php");
?>
