<?php
require_once __DIR__ . '/../../../include/SecretariaGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_inventario');
require_once __DIR__ . "/../../../modelos/inventario.php";
require_once __DIR__ . "/../../../modelos/log.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$ok = false; $msg = 'ID no especificado';

if (!Security::validateCSRFToken(null, false)) {
    if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']); exit; }
    header("Location: ../../../vistas/secretaria/inventario/verInventario.php"); exit;
}

if (isset($_POST['idArticulo'])) {
    $idArticulo = (int)($_POST['idArticulo'] ?? 0);
    if (eliminarArticulo($idArticulo)) {
        registrarAccionSecretaria('borrar', 'inventario', $idArticulo);
        $ok = true; $msg = "Artículo eliminado.";
        $_SESSION['exito'] = $msg;
    } else {
        $msg = "No se pudo eliminar el artículo.";
        $_SESSION['errores'] = $msg;
    }
}

if ($isAjax) {
    header('Content-Type: application/json');
    unset($_SESSION['exito'], $_SESSION['errores']);
    echo json_encode(['ok' => $ok, 'msg' => $msg]);
    exit;
}
header("Location: ../../../vistas/secretaria/inventario/verInventario.php");
exit;
?>
