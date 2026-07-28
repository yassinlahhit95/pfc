<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_fp_dual');
require_once __DIR__ . "/../../../modelos/fp_dual.php";
require_once __DIR__ . "/../../../modelos/log.php";

if (isset($_POST['guardarEmpresa'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/admin/fp_dual/verEmpresas.php");
        exit;
    }

    $idEmpresa = (int)($_POST['idEmpresa'] ?? 0);
    $nombre    = trim($_POST['nombre']);
    $cif       = trim($_POST['cif'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $contacto  = trim($_POST['contacto'] ?? '');
    $telefono  = trim($_POST['telefono'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $activo    = (int)($_POST['activo'] ?? 1);

    $errores = [];
    if (empty($nombre)) $errores['nombre'] = "El nombre de la empresa es obligatorio.";

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_empresa'] = $_POST;
        header("Location: ../../../vistas/admin/fp_dual/editarEmpresa.php?idEmpresa=$idEmpresa");
        exit;
    }

    if (actualizarEmpresa($idEmpresa, $nombre, $cif, $direccion, $contacto, $telefono, $email, $activo)) {
        registrarAccion('actualizar', 'fp_empresas', $idEmpresa, $nombre);
        $_SESSION['exito'] = "La empresa ha sido actualizada correctamente.";
        header("Location: ../../../vistas/admin/fp_dual/verEmpresas.php");
        exit;
    }
    
    $_SESSION['errores'] = "Ocurrió un error al intentar actualizar la empresa.";
    header("Location: ../../../vistas/admin/fp_dual/editarEmpresa.php?idEmpresa=$idEmpresa");
    exit;
}
header("Location: ../../../vistas/admin/fp_dual/verEmpresas.php");
exit;
