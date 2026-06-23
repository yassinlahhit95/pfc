<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/tutores.php";
require_once __DIR__ . "/../../../modelos/log.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['actualizarTutor'])) {
    header("Location: ../../../vistas/admin/tutores/verTutores.php");
    exit;
}

if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/admin/tutores/verTutores.php");
    exit;
}

$idTutor    = (int)($_POST['idTutor'] ?? 0);
$nombre     = trim($_POST['nombreTutor'] ?? '');
$email      = trim($_POST['emailTutor'] ?? '');
$dni        = trim($_POST['dniTutor'] ?? '');
$telefono   = trim($_POST['telefonoTutor'] ?? '');
$parentesco = trim($_POST['parentesco'] ?? 'Tutor Legal');
$estudiantes = isset($_POST['estudiantes']) && is_array($_POST['estudiantes'])
    ? $_POST['estudiantes'] : [];

$errores = [];
if ($idTutor <= 0) {
    $_SESSION['errores'] = "Tutor no válido.";
    header("Location: ../../../vistas/admin/tutores/verTutores.php");
    exit;
}
if (empty($nombre))    $errores['nombreTutor'] = "El nombre es obligatorio.";
if (empty($email)) {
    $errores['emailTutor'] = "El correo electrónico es obligatorio.";
} elseif (!Security::validateEmail($email)) {
    $errores['emailTutor'] = "El formato del correo electrónico no es válido.";
}
if (empty($dni)) $errores['dniTutor'] = "El DNI/NIE es obligatorio.";

if (empty($errores)) {
    $con = obtenerConexion();
    $chk = mysqli_prepare($con,
        "SELECT idTutor FROM tutores WHERE (dniTutor=? OR emailTutor=?) AND idTutor != ?");
    mysqli_stmt_bind_param($chk, "ssi", $dni, $email, $idTutor);
    mysqli_stmt_execute($chk);
    if (mysqli_num_rows(mysqli_stmt_get_result($chk)) > 0) {
        $errores['dniTutor'] = "Ya existe otro familiar con ese DNI o correo electrónico.";
    }
}

if (!empty($errores)) {
    $_SESSION['errores'] = $errores;
    $_SESSION['datos_tutor'] = $_POST;
    header("Location: ../../../vistas/admin/tutores/modificarTutor.php?idTutor={$idTutor}");
    exit;
}

$ok = actualizarTutor($idTutor, $nombre, $email, $dni, $telefono);
if ($ok) {
    actualizarVinculacionesTutor($idTutor, $estudiantes, $parentesco);
    registrarAccion('actualizar', 'tutores', $idTutor, $nombre);
    $_SESSION['exito'] = "Datos del familiar actualizados correctamente.";
    header("Location: ../../../vistas/admin/tutores/verTutores.php");
    exit;
}

$_SESSION['errores'] = "Error al actualizar el familiar. Inténtelo de nuevo.";
$_SESSION['datos_tutor'] = $_POST;
header("Location: ../../../vistas/admin/tutores/modificarTutor.php?idTutor={$idTutor}");
exit;
