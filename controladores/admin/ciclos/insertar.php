<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/log.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['guardarCiclo'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/admin/ciclos/agregarCiclos.php");
        exit;
    }
    $nombre = trim($_POST['nombreCiclo']);
    $abreviatura = trim($_POST['abreviaturaCiclo']);
    $idNivelEducativo = (int)($_POST['idNivel'] ?? 0);
    $precioCiclo = trim($_POST['precioCiclo']);
    $profesores = $_POST['profesores'] ?? [];

    // ── Validación ──
    $errores = [];
    if (empty($nombre)) $errores['nombreCiclo'] = "El nombre del ciclo formativo es un campo obligatorio.";
    if (empty($abreviatura)) $errores['abreviaturaCiclo'] = "La abreviatura del ciclo formativo es un campo obligatorio.";
    if (empty($idNivelEducativo)) $errores['idNivel'] = "El nivel educativo es un campo obligatorio.";
    if (!is_numeric($precioCiclo) || $precioCiclo < 0) $errores['precioCiclo'] = "El precio debe ser un valor numérico válido y no negativo.";
    if (empty($errores) && checkCicloExistente($nombre, $abreviatura)) {
        $errores['nombreCiclo'] = "El nombre o la abreviatura especificados ya se encuentran registrados en el sistema.";
    }

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_ciclo'] = $_POST;
        header("Location: ../../../vistas/admin/ciclos/agregarCiclos.php");
        exit;
    }

    if (insertarNuevoCiclo($nombre, $abreviatura, $idNivelEducativo, $profesores, $precioCiclo)) {
        registrarAccion('insertar', 'ciclos', null, "$nombre ($abreviatura)");
        $_SESSION['exito'] = "El ciclo formativo ha sido registrado correctamente.";
        header("Location: ../../../vistas/admin/ciclos/verCiclos.php");
        exit;
    }
    $_SESSION['errores'] = "Ocurrió un error al intentar registrar el nuevo ciclo formativo.";
    header("Location: ../../../vistas/admin/ciclos/agregarCiclos.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/admin/ciclos/verCiclos.php");
exit;
