<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
require_once __DIR__ . "/../../../modelos/aula.php";

$idProfesor = $_SESSION['idProfesor'];
$idModulo   = intval($_POST['idModulo'] ?? 0);
$nombre     = trim($_POST['nombre'] ?? '');
$color      = trim($_POST['color'] ?? '#0ea5e9');
$icono      = trim($_POST['icono'] ?? 'fa-folder');
$idPadre    = intval($_POST['idPadre'] ?? 0) ?: null;

// Validar el color hexadecimal y el icono (FontAwesome)
if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) $color = '#0ea5e9';
if (!preg_match('/^fa-[a-z0-9-]+$/', $icono))    $icono = 'fa-folder';

// La carpeta padre, si se indica, debe pertenecer al mismo módulo
if ($idPadre) {
    $padre = obtenerCarpetaAulaPorId($idPadre);
    if (!$padre || $padre['idModulo'] != $idModulo) $idPadre = null;
}

if ($idModulo > 0 && !empty($nombre)) {
    if (insertarCarpetaAula($nombre, $idModulo, $idProfesor, $color, $icono, $idPadre)) {
        $_SESSION['exito'] = "Carpeta creada.";
    } else {
        $_SESSION['errores'] = "No se pudo crear la carpeta.";
    }
}
$destino = "../../../vistas/profesores/aula/recursos.php?id=$idModulo";
if ($idPadre) $destino .= "&carpeta=$idPadre";
header("Location: $destino");
exit;
