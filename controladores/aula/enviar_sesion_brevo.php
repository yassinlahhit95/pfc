<?php
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

$idProfesor = $_SESSION['idProfesor'];
$idSesion = (int)($_GET['id'] ?? 0);

if (!$idSesion) {
    $_SESSION['errores'] = 'ID de sesión inválido';
    header("Location: ../../vistas/profesores/aula/sesiones.php");
    exit;
}

// Obtener sesión
$sesion = obtenerSesionPorId($idSesion);

if (!$sesion || $sesion['idProfesor'] != $idProfesor) {
    $_SESSION['errores'] = 'No tienes permiso para enviar esta sesión';
    Logger::warning('Intento no autorizado de enviar sesión', ['profesor' => $idProfesor, 'sesion' => $idSesion]);
    header("Location: ../../vistas/profesores/aula/sesiones.php");
    exit;
}

// Obtener el ciclo del módulo
$modulo = obtenerModuloPorId($sesion['idModulo']);
if (!$modulo) {
    $_SESSION['errores'] = 'No se encontró el módulo asociado';
    Logger::error('Módulo no encontrado', ['modulo' => $sesion['idModulo']]);
    header("Location: ../../vistas/profesores/aula/sesiones.php");
    exit;
}

$idCiclo = $modulo['idCiclo'];

// Obtener estudiantes del ciclo
$estudiantes = listarEstudiantesPorCiclo($idCiclo);

if (empty($estudiantes)) {
    $_SESSION['errores'] = 'No hay estudiantes en este ciclo';
    Logger::warning('Sin estudiantes en ciclo', ['ciclo' => $idCiclo, 'modulo' => $sesion['idModulo']]);
    header("Location: ../../vistas/profesores/aula/sesiones.php");
    exit;
}

error_log("DEBUG: Encontrados " . count($estudiantes) . " estudiantes en ciclo $idCiclo");

// Generar HTML del email
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

// Enviar emails
$enviados = 0;
$errores = [];
$emailsInvalidos = [];

foreach ($estudiantes as $estudiante) {
    $email = trim($estudiante['email'] ?? '');
    $nombre = htmlspecialchars($estudiante['nombre'] ?? 'Estudiante');

    if (empty($email)) {
        error_log("DEBUG: Email vacío para estudiante: $nombre");
        $emailsInvalidos[] = $nombre;
        continue;
    }

    error_log("DEBUG: Enviando email a $email para $nombre");

    if (sendEmail($email, "Sesión en Vivo: $titulo", $htmlContent)) {
        $enviados++;
        error_log("DEBUG: Email enviado exitosamente a $email");
    } else {
        error_log("DEBUG: Error enviando email a $email. Último error: " . ($_SESSION['ultimo_error_email'] ?? 'Desconocido'));
        $errores[] = $nombre;
    }
}

// Registrar actividad
Logger::activity('SESION_ENVIADA', $idProfesor, [
    'idSesion' => $idSesion,
    'titulo' => $sesion['titulo'],
    'enviados' => $enviados,
    'total' => count($estudiantes),
    'ciclo' => $idCiclo
]);

// Preparar mensaje de resultado
if ($enviados > 0) {
    $_SESSION['exito'] = "✅ Sesión enviada a $enviados estudiante" . ($enviados != 1 ? 's' : '');
    if (!empty($errores)) {
        $_SESSION['exito'] .= " ⚠️ Error con: " . implode(', ', $errores);
    }
    if (!empty($emailsInvalidos)) {
        $_SESSION['exito'] .= " ℹ️ Sin email: " . implode(', ', $emailsInvalidos);
    }
} else {
    error_log("DEBUG: Fallo total enviando sesión. Estudiantes encontrados: " . count($estudiantes));
    if (!empty($emailsInvalidos)) {
        $_SESSION['errores'] = 'Los estudiantes del ciclo no tienen emails registrados';
    } else {
        $_SESSION['errores'] = 'Error al enviar emails. Por favor intenta nuevamente o contacta con soporte.';
    }
}

header("Location: ../../vistas/profesores/aula/sesiones.php");
?>
