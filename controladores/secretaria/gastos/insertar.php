<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../modelos/gastos.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../vistas/secretaria/gastos/agregarGasto.php");
    exit;
}

$idCategoria      = (int)($_POST['idCategoria'] ?? 0);
$idCiclo          = (int)($_POST['idCiclo'] ?? 0) ?: null;
$concepto         = Security::sanitize($_POST['concepto'] ?? '');
$importe          = (float)($_POST['importe'] ?? 0);
$fecha            = Security::sanitize($_POST['fecha'] ?? '');
$tipoJustificante = Security::sanitize($_POST['tipoJustificante'] ?? '');
$numeroReferencia = Security::sanitize($_POST['numeroReferencia'] ?? '');
$observaciones    = Security::sanitize($_POST['observaciones'] ?? '');

$errores = [];
if ($idCategoria <= 0) $errores[] = "Debes seleccionar una categoría.";
if (empty($concepto))  $errores[] = "El concepto es obligatorio.";
if ($importe <= 0)     $errores[] = "El importe debe ser mayor que 0.";
if (empty($fecha))     $errores[] = "La fecha es obligatoria.";

if ($errores) {
    $_SESSION['errores'] = $errores;
    header("Location: ../../../vistas/secretaria/gastos/agregarGasto.php");
    exit;
}

$ok = insertarGasto($idCategoria, $idCiclo, $concepto, $importe, $fecha,
                    $tipoJustificante, $numeroReferencia, '', $observaciones);

if ($ok) {
    $_SESSION['exito'] = "Gasto registrado correctamente.";
} else {
    $_SESSION['errores'] = "Error al registrar el gasto.";
}
header("Location: ../../../vistas/secretaria/gastos/verGastos.php");
exit;
