<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../modelos/inventario.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../vistas/secretaria/inventario/gestionarPrestamos.php");
    exit;
}

$idPrestamo = (int)($_POST['idPrestamo'] ?? 0);

if ($idPrestamo <= 0) {
    $_SESSION['errores'] = "Préstamo no válido.";
    header("Location: ../../../vistas/secretaria/inventario/gestionarPrestamos.php");
    exit;
}

$ok = devolverPrestamo($idPrestamo);

if ($ok) {
    $_SESSION['exito'] = "Devolución registrada correctamente.";
} else {
    $_SESSION['errores'] = "Error al registrar la devolución.";
}
header("Location: ../../../vistas/secretaria/inventario/gestionarPrestamos.php");
exit;
