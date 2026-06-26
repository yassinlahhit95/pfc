<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/SecretariaGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_inventario');
require_once __DIR__ . "/../../../modelos/inventario.php";
require_once __DIR__ . "/../../../modelos/log.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['registrarPrestamo'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/secretaria/inventario/agregarPrestamo.php");
        exit;
    }
    $idArticulo    = (int)($_POST['idArticulo'] ?? 0);
    $idEstudiante  = (int)($_POST['idEstudiante'] ?? 0);
    $fechaPrestamo = trim($_POST['fechaPrestamo']);

    $errores = [];
    if (empty($idArticulo))    $errores['idArticulo'] = "Debe seleccionar un equipo del inventario.";
    if (empty($idEstudiante))  $errores['idEstudiante'] = "Debe seleccionar un estudiante.";
    if (empty($fechaPrestamo)) $errores['fechaPrestamo'] = "La fecha del préstamo es un campo obligatorio.";

    if (empty($errores)) {
        if (registrarPrestamo($idEstudiante, $idArticulo, $fechaPrestamo)) {
            registrarAccionSecretaria('prestar', 'inventario', $idArticulo, "Estudiante #$idEstudiante");
            $_SESSION['exito'] = "El préstamo ha sido registrado correctamente.";
            header("Location: ../../../vistas/secretaria/inventario/gestionarPrestamos.php");
            exit;
        }
        $_SESSION['errores'] = "Ocurrió un error al intentar registrar el préstamo.";
    } else {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_prestamo'] = $_POST;
    }

    header("Location: ../../../vistas/secretaria/inventario/agregarPrestamo.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/secretaria/inventario/gestionarPrestamos.php");
exit;
