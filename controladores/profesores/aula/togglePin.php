<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
// Fija o desfija un archivo o carpeta. Solo el profesor propietario.
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
$idProfesor = $_SESSION['idProfesor'];
$tipo       = $_POST['tipo'] ?? '';       // archivo | carpeta
$id         = intval($_POST['id'] ?? 0);
$idModulo   = intval($_POST['modulo'] ?? 0);
$carpeta    = intval($_POST['carpeta'] ?? 0);
$fijado     = null;                        // nuevo estado tras alternar

if ($id > 0 && in_array($tipo, ['archivo', 'carpeta'])) {
    if ($tipo === 'archivo') {
        $item = obtenerArchivoPorId($id);
        if ($item && $item['idProfesor'] == $idProfesor) {
            togglePinArchivoAula($id);
            $idModulo = $idModulo ?: $item['idModulo'];
            $fijado   = $item['fijado'] ? 0 : 1;
        }
    } else {
        $item = obtenerCarpetaAulaPorId($id);
        if ($item && $item['idProfesor'] == $idProfesor) {
            togglePinCarpetaAula($id);
            $idModulo = $idModulo ?: $item['idModulo'];
            $fijado   = $item['fijado'] ? 0 : 1;
        }
    }
}

$destino = "../../../vistas/profesores/aula/recursos.php?id=$idModulo";
if ($carpeta) $destino .= "&carpeta=$carpeta";
responderAccionAula($esAjax, $fijado !== null, $destino, ['fijado' => $fijado]);
