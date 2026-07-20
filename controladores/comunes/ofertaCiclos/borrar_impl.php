<?php
// ══════════════════════════════════════════════════════════════════════
// Implementación compartida de controladores/{admin,secretaria}/ofertaCiclos/borrar.php
// El wrapper de cada rol ya validó el Guard correspondiente y debe definir
// $rolBase ('admin' | 'secretaria') antes de hacer require de este archivo.
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../modelos/landingCiclos.php";
require_once __DIR__ . "/../../../modelos/log.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$ok = false; $msg = 'ID no especificado';

if (!Security::validateCSRFToken(null, false)) {
    if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']); exit; }
    header("Location: ../../../vistas/$rolBase/ofertaCiclos/gestion.php"); exit;
}

if (isset($_POST['idLandingCiclo'])) {
    $idLandingCiclo = (int)($_POST['idLandingCiclo'] ?? 0);
    $ciclo = obtenerCicloLandingPorId($idLandingCiclo);
    if ($ciclo && borrarCicloLanding($idLandingCiclo)) {
        // La imagen de portada deja de usarse: se elimina del disco
        if (!empty($ciclo['imagen'])) {
            $ruta = __DIR__ . '/../../../public/uploads/ofertaCiclos/' . basename($ciclo['imagen']);
            if (is_file($ruta)) @unlink($ruta);
        }
        $rolBase === 'secretaria'
            ? registrarAccionSecretaria('borrar', 'ofertaCiclos', $idLandingCiclo, $ciclo['titulo'])
            : registrarAccion('borrar', 'ofertaCiclos', $idLandingCiclo, $ciclo['titulo']);
        $ok = true; $msg = "El ciclo ha sido eliminado correctamente.";
        $_SESSION['exito'] = $msg;
    } else {
        $msg = "Ocurrió un error al intentar eliminar el ciclo seleccionado.";
        $_SESSION['errores'] = $msg;
    }
}

if ($isAjax) {
    header('Content-Type: application/json');
    unset($_SESSION['exito'], $_SESSION['errores']);
    echo json_encode(['ok' => $ok, 'msg' => $msg]);
    exit;
}
header("Location: ../../../vistas/$rolBase/ofertaCiclos/gestion.php");
exit;
