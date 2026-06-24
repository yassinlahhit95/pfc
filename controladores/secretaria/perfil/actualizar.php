<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../modelos/secretarias.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../vistas/secretaria/perfil/ver.php");
    exit;
}

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/secretaria/perfil/editar.php"); exit;
}

$idSecretaria = (int)$_SESSION['idSecretaria'];
$nombre       = Security::sanitize($_POST['nombre'] ?? '');
$email        = strtolower(trim($_POST['email'] ?? ''));
$nuevaPass    = $_POST['nueva_password'] ?? '';
$confirmaPass = $_POST['confirmar_password'] ?? '';

$errores = [];
if (empty($nombre)) $errores[] = "El nombre es obligatorio.";
if (empty($email) || !Security::validateEmail($email)) $errores[] = "Email no válido.";

if (!empty($nuevaPass)) {
    if (strlen($nuevaPass) < 8) {
        $errores[] = "La contraseña debe tener al menos 8 caracteres.";
    } elseif ($nuevaPass !== $confirmaPass) {
        $errores[] = "Las contraseñas no coinciden.";
    }
}

if ($errores) {
    $_SESSION['errores'] = $errores;
    header("Location: ../../../vistas/secretaria/perfil/editar.php");
    exit;
}

$ok = actualizarSecretaria($idSecretaria, $nombre, $email);

if ($ok && !empty($nuevaPass)) {
    $hash = password_hash($nuevaPass, PASSWORD_BCRYPT, ['cost' => 12]);
    actualizarPasswordSecretaria($idSecretaria, $hash);
}

if ($ok) {
    $_SESSION['exito'] = "Perfil actualizado correctamente.";
} else {
    $_SESSION['errores'] = "Error al actualizar el perfil.";
}
header("Location: ../../../vistas/secretaria/perfil/ver.php");
exit;
