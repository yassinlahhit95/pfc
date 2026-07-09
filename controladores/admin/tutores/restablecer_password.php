<?php
// ══════════════════════════════════════════════════════════════════════
// RESTABLECER CONTRASEÑA DE UN TUTOR (Sistema Parental) — dirección
// Genera una contraseña temporal, la envía por email y obliga a cambiarla.
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/tutores.php";
require_once __DIR__ . "/../../../modelos/log.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Security::validateCSRFToken()) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/admin/tutores/verTutores.php");
    exit;
}

$idTutor = (int)($_POST['idTutor'] ?? 0);
$tutor = $idTutor > 0 ? obtenerTutorPorId($idTutor) : null;

if (!$tutor) {
    $_SESSION['errores'] = 'El familiar indicado no existe.';
} elseif (restablecerPasswordTutor($idTutor)) {
    registrarAccion('actualizar', 'tutores', $idTutor, 'Contraseña restablecida: ' . $tutor['nombreTutor']);
    $_SESSION['exito'] = mensajeExitoConCredenciales(
        "Contraseña de «" . $tutor['nombreTutor'] . "» restablecida correctamente."
    );
} else {
    $_SESSION['errores'] = 'No se pudo restablecer la contraseña. Inténtelo de nuevo.';
}

header("Location: ../../../vistas/admin/tutores/verTutores.php");
exit;
