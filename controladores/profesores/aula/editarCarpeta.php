<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
require_once __DIR__ . "/../../../modelos/aula.php";

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/profesores/aula/recursos.php");
    exit;
}

$idCarpeta  = intval($_POST['idCarpeta'] ?? 0);
$nombre     = trim($_POST['nombre'] ?? '');
$color      = trim($_POST['color'] ?? '#0ea5e9');
$icono      = trim($_POST['icono'] ?? 'fa-folder');
$idModulo   = intval($_POST['idModulo'] ?? 0);

if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) $color = '#0ea5e9';
if (!preg_match('/^fa-[a-z0-9-]+$/', $icono))    $icono = 'fa-folder';

if ($idCarpeta > 0 && !empty($nombre)) {
    $carpeta = obtenerCarpetaAulaPorId($idCarpeta);
    if ($carpeta && $carpeta['idProfesor'] == $_SESSION['idProfesor']) {
        actualizarCarpetaAula($idCarpeta, $nombre, $color, $icono);
        $_SESSION['exito'] = "Carpeta actualizada.";
    }
}
header("Location: ../../../vistas/profesores/aula/recursos.php?id=$idModulo");
exit;
