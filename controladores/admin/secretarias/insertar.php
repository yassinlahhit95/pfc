<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../modelos/secretarias.php';
require_once __DIR__ . '/../../../modelos/log.php';
require_once __DIR__ . '/../../../include/credenciales.php';

if (!isset($_POST['guardarSecretaria'])) {
    header("Location: ../../../vistas/admin/secretarias/verSecretarias.php");
    exit;
}

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/admin/secretarias/agregarSecretaria.php");
    exit;
}

$nombre = trim($_POST['nombreSecretaria'] ?? '');
$email  = strtolower(trim($_POST['emailSecretaria'] ?? ''));

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
    header("Location: ../../../vistas/admin/secretarias/agregarSecretaria.php");
    exit;
}

$idNuevo = insertarSecretaria($nombre, $email);

if ($idNuevo) {
    registrarAccion('insertar', 'secretarias', $idNuevo, $nombre);
    $_SESSION['exito'] = mensajeExitoConCredenciales("La secretaria ha sido registrada correctamente.");
    header("Location: ../../../vistas/admin/secretarias/verSecretarias.php");
    exit;
}

$_SESSION['errores'] = "Error al registrar la secretaria. El correo puede estar en uso.";
$_SESSION['datos_secretaria'] = $_POST;
header("Location: ../../../vistas/admin/secretarias/agregarSecretaria.php");
exit;
