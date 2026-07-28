<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_fp_dual');
require_once __DIR__ . "/../../../modelos/fp_dual.php";
require_once __DIR__ . "/../../../modelos/log.php";

if (isset($_POST['guardarEmpresa'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/admin/fp_dual/agregarEmpresa.php");
        exit;
    }

    $nombre    = trim($_POST['nombre']);
    $cif       = trim($_POST['cif'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $contacto  = trim($_POST['contacto'] ?? '');
    $telefono  = trim($_POST['telefono'] ?? '');
    $email     = trim($_POST['email'] ?? '');

    $errores = [];
    if (empty($nombre)) $errores['nombre'] = "El nombre de la empresa es obligatorio.";

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_empresa'] = $_POST;
        header("Location: ../../../vistas/admin/fp_dual/agregarEmpresa.php");
        exit;
    }

    if (insertarEmpresa($nombre, $cif, $direccion, $contacto, $telefono, $email)) {
        registrarAccion('insertar', 'fp_empresas', null, $nombre);
        $_SESSION['exito'] = "La empresa ha sido registrada correctamente.";
        header("Location: ../../../vistas/admin/fp_dual/verEmpresas.php");
        exit;
    }
    
    $_SESSION['errores'] = "Ocurrió un error al intentar registrar la empresa.";
    header("Location: ../../../vistas/admin/fp_dual/agregarEmpresa.php");
    exit;
}
header("Location: ../../../vistas/admin/fp_dual/verEmpresas.php");
exit;
