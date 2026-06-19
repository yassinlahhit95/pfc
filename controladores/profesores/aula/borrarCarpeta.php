<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
// Mueve una carpeta (y su contenido) a la papelera. Solo el profesor propietario.
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
$idCarpeta = intval($_POST['id'] ?? 0);
$idModulo  = intval($_POST['modulo'] ?? 0);
$carpeta   = intval($_POST['carpeta'] ?? 0); // carpeta que se está visualizando
$ok        = false;

if ($idCarpeta > 0) {
    $c = obtenerCarpetaAulaPorId($idCarpeta);
    if ($c && $c['idProfesor'] == $_SESSION['idProfesor']) {
        eliminarDefinitivoCarpetaRecursivoAula($idCarpeta);
        if (!$esAjax) $_SESSION['exito'] = "Carpeta eliminada definitivamente.";
        $idModulo = $idModulo ?: $c['idModulo'];
        $ok = true;
    }
}

$destino = "../../../vistas/profesores/aula/recursos.php?id=$idModulo";
if ($carpeta) $destino .= "&carpeta=$carpeta";
responderAccionAula($esAjax, $ok, $destino);
