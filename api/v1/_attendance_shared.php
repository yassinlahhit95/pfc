<?php
declare(strict_types=1);

// Shared by attendance.php and attendance-resolve.php. Underscore-prefixed
// per api/v1/.htaccess convention — blocks direct HTTP access to this file.

require_once __DIR__ . '/../../include/R2Client.php';

// Same local-file-first-then-R2-presigned-URL resolution the web app uses
// (vistas/profesores/asistencias/justificaciones.php) — just built as an
// absolute URL since the mobile client isn't rendering relative web paths.
function justificanteUrl(string $archivo): string {
    $archivoNombre = basename($archivo);
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $urlLocal = "$scheme://$host/public/uploads/justificantes/$archivoNombre";
    return R2Client::documentoUrl(
        __DIR__ . '/../../public/uploads/justificantes/' . $archivoNombre,
        $urlLocal,
        'justificantes/' . $archivoNombre
    );
}

// Push al estudiante y a sus tutores vinculados cuando una justificación se
// resuelve (aprobada/rechazada) — usado tanto por attendance-resolve.php
// (profesor resolviendo una solicitud pendiente) como por attendance-justify.php
// (staff auto-aprobando la suya propia). Extraído de attendance-resolve.php
// para no duplicar el bloque FCM en los dos sitios.
function notificarJustificacionResuelta(int $idEstudiante, int $idAsistencia, bool $aprobar, string $motivoRechazo): void {
    $fh = __DIR__ . '/../../controladores/firebase/firebase_helper.php';
    if (!file_exists($fh)) return;
    require_once $fh;

    $titulo = $aprobar ? 'Justificación aprobada' : 'Justificación rechazada';
    $mensaje = $aprobar
        ? 'Tu justificación de falta ha sido aprobada.'
        : 'Tu justificación de falta ha sido rechazada' . ($motivoRechazo !== '' ? ": $motivoRechazo" : '.');

    $con = obtenerConexion();
    $destinatarios = [['id' => $idEstudiante, 'rol' => 'estudiante']];
    $st = mysqli_prepare($con, 'SELECT idTutor FROM estudiante_tutor WHERE idEstudiante = ?');
    mysqli_stmt_bind_param($st, 'i', $idEstudiante);
    mysqli_stmt_execute($st);
    foreach (mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC) as $row) {
        $destinatarios[] = ['id' => (int)$row['idTutor'], 'rol' => 'tutor'];
    }
    foreach ($destinatarios as $d) {
        $token = obtenerTokenUsuario($d['id'], $d['rol']);
        if ($token) {
            enviarNotificacionFirebase($token, $titulo, $mensaje, 'asistencia_resuelta', ['idAsistencia' => $idAsistencia]);
        }
    }
}
