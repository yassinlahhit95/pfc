<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../modelos/pagos.php";
require_once __DIR__ . "/../../../modelos/log.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../vistas/secretaria/pagos/agregarPago.php");
    exit;
}

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/secretaria/pagos/agregarPago.php"); exit;
}

$idEstudiante   = (int)($_POST['idEstudiante'] ?? 0);
$monto          = (float)($_POST['monto'] ?? 0);
$tipoPago       = Security::sanitize($_POST['tipoPago'] ?? '');
$fechaPago      = Security::sanitize($_POST['fechaPago'] ?? '');
$fechaProximo   = Security::sanitize($_POST['fechaProximoPago'] ?? '');

$errores = [];
if ($idEstudiante <= 0) $errores[] = "Debes seleccionar un estudiante.";
if ($monto <= 0)        $errores[] = "El importe debe ser mayor que 0.";
if (empty($tipoPago))   $errores[] = "El tipo de pago es obligatorio.";
if (empty($fechaPago))  $errores[] = "La fecha de pago es obligatoria.";

if ($errores) {
    $_SESSION['errores'] = $errores;
    header("Location: ../../../vistas/secretaria/pagos/agregarPago.php");
    exit;
}

$ok = insertarPagoCompleto($idEstudiante, $monto, $tipoPago, $fechaPago, $fechaProximo ?: null);

if ($ok) {
    registrarAccionSecretaria('insertar', 'pagos', null, "$tipoPago — {$monto}€");
    $_SESSION['exito'] = "Pago registrado correctamente.";
} else {
    $_SESSION['errores'] = "Error al registrar el pago.";
}
header("Location: ../../../vistas/secretaria/pagos/verPagos.php");
exit;
