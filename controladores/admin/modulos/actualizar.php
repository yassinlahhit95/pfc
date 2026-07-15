<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/log.php";
require_once __DIR__ . "/../../../modelos/academico_config.php";

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
    $codigoModulo = trim($_POST['codigoModulo'] ?? '') !== '' ? trim($_POST['codigoModulo']) : null;
    $idCiclo      = (int)($_POST['idCiclo'] ?? 0);
    $horasMaximas = trim($_POST['horasMaximas']);
    $cursoAnioPost = trim($_POST['cursoAnio'] ?? '');
    $cursoAnio    = existeNombreCursoEnCiclo($idCiclo, $cursoAnioPost) && $cursoAnioPost !== '' ? $cursoAnioPost : null;
    $creditosECTS = is_numeric($_POST['creditosECTS'] ?? '') ? (int)$_POST['creditosECTS'] : null;
    $tiposPermitidos = ['Específico', 'Transversal', 'Proyecto', 'Empresa'];
    $tipoModulo   = in_array($_POST['tipoModulo'] ?? '', $tiposPermitidos, true) ? $_POST['tipoModulo'] : 'Específico';

    $errores = [];
    if (empty($nombre))      $errores['nombreModulo'] = "El nombre del módulo es un campo obligatorio.";
    if (empty($idCiclo))     $errores['idCiclo'] = "Debe seleccionar un ciclo formativo.";
    if (empty($horasMaximas)) {
        $errores['horasMaximas'] = "Las horas totales del módulo son un campo obligatorio.";
    } elseif (!is_numeric($horasMaximas)) {
        $errores['horasMaximas'] = "Las horas deben ser un valor numérico.";
    }

    if (empty($errores) && checkModuloExistente($nombre, $idCiclo, $idModulo)) {
        $errores['nombreModulo'] = "Ya existe otro módulo con este nombre en el ciclo seleccionado.";
    }

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_modulo'] = $_POST;
        header("Location: ../../../vistas/admin/modulos/modificarModulos.php?idModulo=$idModulo");
        exit;
    }

    if (actualizarModulo($idModulo, $nombre, $idCiclo, $horasMaximas, $cursoAnio, $creditosECTS, $tipoModulo, $codigoModulo)) {
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
