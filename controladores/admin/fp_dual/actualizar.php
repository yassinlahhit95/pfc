<?php
require_once __DIR__ . '/../../../include/Security.php';
Security::initSession();
if (empty($_SESSION['idAdmin']) && empty($_SESSION['idSecretaria'])) {
    header('Location: /vistas/login.php');
    exit;
}
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

    $idEmpresa       = (int)($_POST['idEmpresa'] ?? 0);
    $nombre          = trim($_POST['nombre']);
    $cif             = trim($_POST['cif'] ?? '');
    $direccion       = trim($_POST['direccion'] ?? '');
    $contacto        = trim($_POST['contacto'] ?? '');
    $telefono        = trim($_POST['telefono'] ?? '');
    $email           = trim($_POST['email'] ?? '');
    $activo          = (int)($_POST['activo'] ?? 1);
    $nuevaPassword   = trim($_POST['nuevaPassword'] ?? '');
    $repetirPassword = trim($_POST['repetirPassword'] ?? '');

    $errores = [];
    if (empty($nombre)) $errores['nombre'] = "El nombre de la empresa es obligatorio.";
    
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores['email'] = "El formato de email no es válido.";
    }

    if (!empty($nuevaPassword)) {
        if (strlen($nuevaPassword) < 8) {
            $errores['nuevaPassword'] = "La contraseña debe tener al menos 8 caracteres.";
        } elseif ($nuevaPassword !== $repetirPassword) {
            $errores['repetirPassword'] = "Las contraseñas no coinciden.";
        }
    }

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_empresa'] = $_POST;
        header("Location: ../../../vistas/admin/fp_dual/editarEmpresa.php?idEmpresa=$idEmpresa");
        exit;
    }

    $passParam = !empty($nuevaPassword) ? $nuevaPassword : null;

    if (actualizarEmpresa($idEmpresa, $nombre, $cif, $direccion, $contacto, $telefono, $email, $activo, $passParam)) {
        $rolEditor = !empty($_SESSION['idSecretaria']) ? 'Secretaría' : 'Director';
        registrarAccion('actualizar', 'fp_empresas', $idEmpresa, $nombre . (!empty($passParam) ? ' (Contraseña modificada)' : '') . " por $rolEditor");
        $_SESSION['exito'] = "La empresa ha sido actualizada correctamente." . (!empty($passParam) ? ' Contraseña actualizada.' : '');
        header("Location: ../../../vistas/admin/fp_dual/verEmpresas.php");
        exit;
    }
    
    $_SESSION['errores'] = "Ocurrió un error al intentar actualizar la empresa.";
    header("Location: ../../../vistas/admin/fp_dual/editarEmpresa.php?idEmpresa=$idEmpresa");
    exit;
}
header("Location: ../../../vistas/admin/fp_dual/verEmpresas.php");
exit;
