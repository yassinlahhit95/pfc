<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . '/../../../modelos/profesores.php';

if (!isset($_POST['actualizarPerfil'])) {
    header("Location: ../../../vistas/profesores/perfil/ver.php");
    exit;
}

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/profesores/perfil/editar.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// VALIDACIÓN
// ══════════════════════════════════════════════════════════════════════
$idProfesor     = (int)$_SESSION['idProfesor'];
$nombre         = Security::sanitize($_POST['nombreProfesor']);
$email          = strtolower(trim($_POST['emailProfesor']));
$telefono       = trim($_POST['telefonoProfesor']);
$passwordActual = trim($_POST['current_password'] ?? '');
$passwordNueva  = trim($_POST['new_password'] ?? '');
$dni            = Security::sanitize($_POST['dniProfesor'] ?? '');
$fechaNac       = Security::sanitize($_POST['fechaNacimientoProfesor'] ?? '');
$direccion      = Security::sanitize($_POST['direccionProfesor'] ?? '');
$ciudad         = Security::sanitize($_POST['ciudadProfesor'] ?? '');
$codigoPostal   = Security::sanitize($_POST['codigoPostalProfesor'] ?? '');
$observaciones  = Security::sanitize($_POST['observacionesProfesor'] ?? '');

$errores = [];

if (empty($idProfesor)) {
    header("Location: ../../../vistas/profesores/perfil/ver.php");
    exit;
}

if (empty($nombre))                               $errores['nombreProfesor']   = "El nombre es obligatorio.";
if (empty($email))                                $errores['emailProfesor']    = "El correo es obligatorio.";
elseif (!Security::validateEmail($email))         $errores['emailProfesor']    = "El formato del correo no es válido.";
if (!empty($telefono) && !Security::validatePhone($telefono)) $errores['telefonoProfesor'] = "El teléfono debe tener 9 dígitos y comenzar por 6, 7, 8 o 9.";

if (!empty($passwordNueva)) {
    if (empty($passwordActual)) {
        $errores['current_password'] = "Ingresa la contraseña actual.";
    } else {
        $datosProfesor = obtenerProfesorPorId($idProfesor);
        if (!$datosProfesor || !password_verify($passwordActual, $datosProfesor['password'])) {
            $errores['current_password'] = "Contraseña actual incorrecta.";
        } elseif (strlen($passwordNueva) < 6) {
            $errores['new_password'] = "Mínimo 6 caracteres.";
        }
    }
}

if (!empty($errores)) {
    $_SESSION['errores'] = $errores;
    $_SESSION['datos_perfil'] = $_POST;
    header("Location: ../../../vistas/profesores/perfil/editar.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (!empty($passwordNueva)) {
    actualizarPasswordProfesor($idProfesor, $passwordNueva);
}

$resultado = actualizarPerfilProfesor($idProfesor, $nombre, $email, $telefono, $dni, $fechaNac ?: null, $direccion, $ciudad, $codigoPostal, $observaciones);

if ($resultado) {
    $_SESSION['exito'] = "Perfil actualizado correctamente.";
    header("Location: ../../../vistas/profesores/perfil/ver.php");
    exit;
} else {
    $_SESSION['errores'] = "Error al actualizar los datos.";
    $_SESSION['datos_perfil'] = $_POST;
    header("Location: ../../../vistas/profesores/perfil/editar.php");
    exit;
}
