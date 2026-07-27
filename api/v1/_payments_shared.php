<?php
declare(strict_types=1);

// Shared by payments.php and payments-resolve.php. Underscore-prefixed
// per api/v1/.htaccess convention — blocks direct HTTP access to this file.

require_once __DIR__ . '/../../include/R2Client.php';

// Same local-file-first-then-R2-presigned-URL resolution as _attendance_shared.php's
// justificanteUrl() — comprobantes live in the same kind of protected R2 bucket.
function comprobanteUrl(string $archivo): string {
    $archivoNombre = basename($archivo);
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $urlLocal = "$scheme://$host/public/uploads/comprobantes/$archivoNombre";
    return R2Client::documentoUrl(
        __DIR__ . '/../../public/uploads/comprobantes/' . $archivoNombre,
        $urlLocal,
        'comprobantes/' . $archivoNombre
    );
}

// Push al estudiante y a sus tutores vinculados cuando un comprobante de pago
// se resuelve (aprobado/rechazado) — mismo patrón que
// _attendance_shared.php's notificarJustificacionResuelta().
function notificarComprobantePagoResuelto(int $idEstudiante, int $idPago, bool $aprobar, string $motivoRechazo): void {
    $fh = __DIR__ . '/../../controladores/firebase/firebase_helper.php';
    if (!file_exists($fh)) return;
    require_once $fh;

    $titulo = $aprobar ? 'Comprobante aprobado' : 'Comprobante rechazado';
    $mensaje = $aprobar
        ? 'Tu comprobante de pago ha sido verificado y aprobado.'
        : 'Tu comprobante de pago ha sido rechazado' . ($motivoRechazo !== '' ? ": $motivoRechazo" : '.');

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
            enviarNotificacionFirebase($token, $titulo, $mensaje, 'pago_comprobante_resuelto', ['idPago' => $idPago]);
        }
    }
}
