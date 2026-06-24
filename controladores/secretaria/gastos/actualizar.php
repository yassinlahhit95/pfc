<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../modelos/gastos.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../vistas/secretaria/gastos/verGastos.php");
    exit;
}

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/secretaria/gastos/verGastos.php"); exit;
}

$idGasto          = (int)($_POST['idGasto'] ?? 0);
$idCategoria      = (int)($_POST['idCategoria'] ?? 0);
$idCiclo          = (int)($_POST['idCiclo'] ?? 0) ?: null;
$concepto         = Security::sanitize($_POST['concepto'] ?? '');
$importe          = (float)($_POST['importe'] ?? 0);
$fecha            = Security::sanitize($_POST['fecha'] ?? '');
$tipoJustificante = Security::sanitize($_POST['tipoJustificante'] ?? '');
$numeroReferencia = Security::sanitize($_POST['numeroReferencia'] ?? '');
$observaciones    = Security::sanitize($_POST['observaciones'] ?? '');

$errores = [];
if ($idGasto <= 0)     $errores[] = "Gasto no válido.";
if ($idCategoria <= 0) $errores[] = "Debes seleccionar una categoría.";
if (empty($concepto))  $errores[] = "El concepto es obligatorio.";
if ($importe <= 0)     $errores[] = "El importe debe ser mayor que 0.";
if (empty($fecha))     $errores[] = "La fecha es obligatoria.";

if ($errores) {
    $_SESSION['errores'] = $errores;
    header("Location: ../../../vistas/secretaria/gastos/modificarGasto.php?id=$idGasto");
    exit;
}

$gastoActual = obtenerGastoPorId($idGasto);
$archivoActual = $gastoActual['archivoJustificante'] ?? '';

$ok = actualizarGasto($idGasto, $idCategoria, $idCiclo, $concepto, $importe, $fecha,
                      $tipoJustificante, $numeroReferencia, $archivoActual, $observaciones);

if ($ok) {
    $_SESSION['exito'] = "Gasto actualizado correctamente.";
} else {
    $_SESSION['errores'] = "Error al actualizar el gasto.";
}
header("Location: ../../../vistas/secretaria/gastos/verGastos.php");
exit;
