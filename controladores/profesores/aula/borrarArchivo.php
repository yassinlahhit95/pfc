<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
// Mueve un archivo a la papelera (soft-delete). Solo el profesor propietario.
// Acepta POST con token CSRF. Responde JSON si la petición es AJAX; si no, redirige.
require_once __DIR__ . "/../../../include/Security.php";
require_once __DIR__ . "/../../../modelos/aula.php";

$esAjax = !empty($_POST['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

if (!function_exists('responderAccionAula')) {
    function responderAccionAula($esAjax, $ok, $destino, $extra = []) {
        if ($esAjax) {
            header('Content-Type: application/json');
            echo json_encode(array_merge(['ok' => $ok], $extra));
        } else {
            header("Location: $destino");
        }
        exit;
    }
}

// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN
// ══════════════════════════════════════════════════════════════════════
if (empty($_SESSION['idProfesor']) || !empty($_SESSION['must_change_password']) || !empty($_SESSION['mfa_setup_required'])) {
    responderAccionAula($esAjax, false, "../../../vistas/login.php", ['error' => 'auth']);
}
if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
    responderAccionAula($esAjax, false, "../../../vistas/profesores/aula/index.php", ['error' => 'csrf']);
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$idArchivo = intval($_POST['id'] ?? 0);
$regresar  = intval($_POST['modulo'] ?? 0);
$ok        = false;
$archivo   = null;

if ($idArchivo > 0) {
    $archivo = obtenerArchivoPorId($idArchivo);
    if ($archivo && $archivo['idProfesor'] == $_SESSION['idProfesor']) {
        eliminarDefinitivoArchivoAula($idArchivo);
        if (!$esAjax) $_SESSION['exito'] = "Archivo eliminado definitivamente.";
        $regresar = $regresar ?: $archivo['idModulo'];
        $ok = true;
    }
}

$destino = "../../../vistas/profesores/aula/recursos.php?id=$regresar";
if (!empty($archivo['idCarpeta'])) $destino .= "&carpeta=" . $archivo['idCarpeta'];
responderAccionAula($esAjax, $ok, $destino);
