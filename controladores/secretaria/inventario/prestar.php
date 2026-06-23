<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../modelos/inventario.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../vistas/secretaria/inventario/gestionarPrestamos.php");
    exit;
}

$idEstudiante  = (int)($_POST['idEstudiante'] ?? 0);
$idArticulo    = (int)($_POST['idArticulo'] ?? 0);
$fechaPrestamo = Security::sanitize($_POST['fechaPrestamo'] ?? '');

$errores = [];
if ($idEstudiante <= 0) $errores[] = "Debes seleccionar un estudiante.";
if ($idArticulo <= 0)   $errores[] = "Debes seleccionar un artículo.";
if (empty($fechaPrestamo)) $errores[] = "La fecha de préstamo es obligatoria.";

if (!$errores) {
    $articulo = obtenerArticuloPorId($idArticulo);
    if (!$articulo || $articulo['estado'] !== 'disponible') {
        $errores[] = "El artículo seleccionado no está disponible.";
    }
}

if ($errores) {
    $_SESSION['errores'] = $errores;
    header("Location: ../../../vistas/secretaria/inventario/gestionarPrestamos.php");
    exit;
}

$ok = registrarPrestamo($idEstudiante, $idArticulo, $fechaPrestamo);

if ($ok) {
    $_SESSION['exito'] = "Préstamo registrado correctamente.";
} else {
    $_SESSION['errores'] = "Error al registrar el préstamo.";
}
header("Location: ../../../vistas/secretaria/inventario/gestionarPrestamos.php");
exit;
