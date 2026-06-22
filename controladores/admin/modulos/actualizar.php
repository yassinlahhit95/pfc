<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/log.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['guardarModulo'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/admin/modulos/verModulos.php");
        exit;
    }
    $idModulo     = (int)($_POST['idModulo'] ?? 0);
    $nombre       = trim($_POST['nombreModulo']);
    $idCiclo      = (int)($_POST['idCiclo'] ?? 0);
    $horasMaximas = trim($_POST['horasMaximas']);
    $aniosPermitidos = ['1º', '2º'];
    $cursoAnio    = in_array($_POST['cursoAnio'] ?? '', $aniosPermitidos, true) ? $_POST['cursoAnio'] : null;
    $creditosECTS = is_numeric($_POST['creditosECTS'] ?? '') ? (int)$_POST['creditosECTS'] : null;

    $errores = '';
    if (empty($nombre))      $errores = "El nombre del módulo es un campo obligatorio.";
    if (empty($idCiclo))     $errores = "Debe seleccionar un ciclo formativo.";
    if (empty($horasMaximas)) {
        $errores = "Las horas totales del módulo son un campo obligatorio.";
    } elseif (!is_numeric($horasMaximas)) {
        $errores = "Las horas deben ser un valor numérico.";
    }

    if (!$errores && checkModuloExistente($nombre, $idCiclo, $idModulo)) {
        $errores = "Ya existe otro módulo con este nombre en el ciclo seleccionado.";
    }

    if ($errores) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_modulo'] = $_POST;
        header("Location: ../../../vistas/admin/modulos/modificarModulos.php?idModulo=$idModulo");
        exit;
    }

    if (actualizarModulo($idModulo, $nombre, $idCiclo, $horasMaximas, $cursoAnio, $creditosECTS)) {
        registrarAccion('actualizar', 'modulos', $idModulo, $nombre);
        $_SESSION['exito'] = "El módulo ha sido actualizado correctamente.";
        header("Location: ../../../vistas/admin/modulos/verModulos.php");
        exit;
    }
    $_SESSION['errores'] = "Ocurrió un error al intentar actualizar el módulo.";
    header("Location: ../../../vistas/admin/modulos/modificarModulos.php?idModulo=$idModulo");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/admin/modulos/verModulos.php");
exit;
