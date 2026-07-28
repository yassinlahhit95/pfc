<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requireJson('feature_modulos');
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/log.php";
require_once __DIR__ . "/../../../modelos/academico_config.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['guardarModulo'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/admin/modulos/agregarModulos.php");
        exit;
    }
    $nombre       = trim($_POST['nombreModulo']);
    $codigoModulo = trim($_POST['codigoModulo'] ?? '') !== '' ? trim($_POST['codigoModulo']) : null;
    $idCiclo      = (int)($_POST['idCiclo'] ?? 0);
    $horasMaximas = trim($_POST['horasMaximas']);
    $cursoAnioPost = trim($_POST['cursoAnio'] ?? '');
    $cursoAnio    = existeNombreCursoEnCiclo($idCiclo, $cursoAnioPost) && $cursoAnioPost !== '' ? $cursoAnioPost : null;
    $creditosECTS = is_numeric($_POST['creditosECTS'] ?? '') ? (int)$_POST['creditosECTS'] : null;
    $tiposPermitidos = ['Específico', 'Transversal', 'Proyecto', 'Empresa'];
    $tipoModulo   = in_array($_POST['tipoModulo'] ?? '', $tiposPermitidos, true) ? $_POST['tipoModulo'] : 'Específico';

    $avisos = [];
    if (empty($nombre))      $avisos['nombreModulo'] = "El nombre del módulo es un campo obligatorio.";
    if (empty($idCiclo))     $avisos['idCiclo'] = "Debe seleccionar un ciclo formativo.";
    else {
        $cicloValido = false;
        $stmt = $GLOBALS['mysqli']->prepare("SELECT 1 FROM ciclos WHERE idCiclo = ? AND activo = 1");
        if ($stmt) {
            $stmt->bind_param('i', $idCiclo);
            $stmt->execute();
            $cicloValido = $stmt->get_result()->num_rows > 0;
            $stmt->close();
        }
        if (!$cicloValido) {
            $avisos['idCiclo'] = "El ciclo formativo seleccionado no es válido o está inactivo.";
        }
    }
    if (empty($horasMaximas)) {
        $avisos['horasMaximas'] = "Las horas máximas son un campo obligatorio.";
    } elseif (!is_numeric($horasMaximas)) {
        $avisos['horasMaximas'] = "Las horas deben ser un valor numérico.";
    }

    if (empty($avisos) && checkModuloExistente($nombre, $idCiclo)) {
        $avisos['nombreModulo'] = "Ya existe otro módulo con este nombre en el ciclo seleccionado.";
    }

    if (!empty($avisos)) {
        $_SESSION['errores'] = $avisos;
        $_SESSION['datos_modulo'] = $_POST;
        header("Location: ../../../vistas/admin/modulos/agregarModulos.php");
        exit;
    }

    $idNuevoModulo = insertarModulo($nombre, $idCiclo, $horasMaximas, $cursoAnio, $creditosECTS, $tipoModulo, $codigoModulo);
    if ($idNuevoModulo) {
        registrarAccion('insertar', 'modulos', $idNuevoModulo, $nombre);
        $_SESSION['exito'] = "El módulo ha sido registrado correctamente.";
        header("Location: ../../../vistas/admin/modulos/verModulos.php");
        exit;
    }
    $_SESSION['errores'] = "Ocurrió un error al intentar registrar el módulo.";
    header("Location: ../../../vistas/admin/modulos/agregarModulos.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/admin/modulos/verModulos.php");
exit;
