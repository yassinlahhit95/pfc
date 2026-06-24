<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../modelos/secretarias.php';
require_once __DIR__ . '/../../../modelos/log.php';

if (!isset($_POST['actualizarSecretaria'])) {
    header("Location: ../../../vistas/admin/secretarias/verSecretarias.php");
    exit;
}

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/admin/secretarias/verSecretarias.php");
    exit;
}

$id     = (int)($_POST['idSecretaria'] ?? 0);
$nombre = trim($_POST['nombreSecretaria'] ?? '');
$email  = strtolower(trim($_POST['emailSecretaria'] ?? ''));

if (!$id) {
    header("Location: ../../../vistas/admin/secretarias/verSecretarias.php");
    exit;
}

$errores = [];
if (empty($nombre)) $errores['nombreSecretaria'] = "El nombre es obligatorio.";
if (empty($email)) {
    $errores['emailSecretaria'] = "El correo es obligatorio.";
} elseif (!Security::validateEmail($email)) {
    $errores['emailSecretaria'] = "Formato de correo no válido.";
}

if (!empty($errores)) {
    $_SESSION['errores'] = $errores;
    $_SESSION['datos_secretaria'] = $_POST;
    header("Location: ../../../vistas/admin/secretarias/modificarSecretaria.php?id=$id");
    exit;
}

if (actualizarSecretaria($id, $nombre, $email)) {
    registrarAccion('actualizar', 'secretarias', $id, $nombre);
    $_SESSION['exito'] = "Secretaria actualizada correctamente.";
} else {
    $_SESSION['errores'] = "Error al actualizar. El correo puede estar en uso.";
}

header("Location: ../../../vistas/admin/secretarias/verSecretarias.php");
exit;
