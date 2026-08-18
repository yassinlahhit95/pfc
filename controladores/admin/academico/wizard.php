<?php
// ══════════════════════════════════════════════════════════════════════
// ASISTENTE DE CONFIGURACIÓN ACADÉMICA — controlador de acciones (AJAX)
// ══════════════════════════════════════════════════════════════════════
// Router por acción, igual estilo que controladores/admisiones/acciones.php.
// Cada acción guarda un paso del asistente y devuelve JSON. El motor de
// notas (modelos/motor_calificaciones.php) no se ve afectado hasta que se
// activa la config con 'activar' — todo lo demás son ediciones sobre
// configuraciones inactivas.
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/academico_config.php";
require_once __DIR__ . "/../../../modelos/plantillas_academicas.php";
require_once __DIR__ . "/../../../modelos/configuracion.php";
require_once __DIR__ . "/../../../modelos/log.php";

header('Content-Type: application/json; charset=utf-8');

if (!Security::validateCSRFToken(null, false)) {
    echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida. Recarga la página e inténtalo de nuevo.']);
    exit;
}

$accion = $_POST['accion'] ?? '';
$idConfig = (int)($_POST['idConfig'] ?? 0);

switch ($accion) {

    case 'crear_config':
        $nombre = trim($_POST['nombre'] ?? '') ?: 'Nueva configuración';
        $tipoEducacion = in_array($_POST['tipoEducacion'] ?? '', ['grado_basico', 'grado_medio', 'grado_superior', 'colegio', 'otro'], true) ? $_POST['tipoEducacion'] : 'otro';
        $anioAcademico = trim($_POST['anioAcademico'] ?? '') ?: null;
        $id = crearConfigAcademicaVacia($nombre, $tipoEducacion, $anioAcademico);
        echo json_encode($id ? ['ok' => true, 'idConfig' => $id] : ['ok' => false, 'msg' => 'No se pudo crear la configuración.']);
        break;

    case 'guardar_general':
        $nombre = trim($_POST['nombre'] ?? '');
        $tipoEducacion = in_array($_POST['tipoEducacion'] ?? '', ['grado_basico', 'grado_medio', 'grado_superior', 'colegio', 'otro'], true) ? $_POST['tipoEducacion'] : 'otro';
        $anioAcademico = trim($_POST['anioAcademico'] ?? '') ?: null;
        if ($nombre === '' || !$idConfig) { echo json_encode(['ok' => false, 'msg' => 'Faltan datos.']); break; }
        echo json_encode(['ok' => actualizarInfoGeneralConfig($idConfig, $nombre, $tipoEducacion, $anioAcademico)]);
        break;

    case 'guardar_curso':
        $idCiclo = (int)($_POST['idCiclo'] ?? 0);
        $idCurso = (int)($_POST['idCurso'] ?? 0) ?: null;
        $nombre = trim($_POST['nombre'] ?? '');
        $orden = (int)($_POST['orden'] ?? 1);
        if (!$idCiclo || $nombre === '') { echo json_encode(['ok' => false, 'msg' => 'Faltan datos.']); break; }
        $id = guardarCursoAcademico($idCiclo, $idCurso, $nombre, $orden);
        echo json_encode($id ? ['ok' => true, 'idCurso' => $id] : ['ok' => false, 'msg' => 'No se pudo guardar.']);
        break;

    case 'eliminar_curso':
        $idCiclo = (int)($_POST['idCiclo'] ?? 0);
        $idCurso = (int)($_POST['idCurso'] ?? 0);
        echo json_encode(['ok' => ($idCiclo && $idCurso) ? eliminarCursoAcademico($idCiclo, $idCurso) : false]);
        break;

    case 'guardar_periodo':
        $idPeriodo = (int)($_POST['idPeriodo'] ?? 0) ?: null;
        $nombre = trim($_POST['nombre'] ?? '');
        $tipo = in_array($_POST['tipo'] ?? '', ['evaluacion','recuperacion','ordinaria','extraordinaria','final','proyecto','practicas','certificacion','otro'], true) ? $_POST['tipo'] : 'evaluacion';
        $fechaInicio = trim($_POST['fechaInicio'] ?? '') ?: null;
        $fechaFin = trim($_POST['fechaFin'] ?? '') ?: null;
        $orden = (int)($_POST['orden'] ?? 1);
        $visible = !empty($_POST['visible']);
        $bloqueado = !empty($_POST['bloqueado']);
        $idPeriodoRecuperaDe = (int)($_POST['idPeriodoRecuperaDe'] ?? 0) ?: null;
        if (!$idConfig || $nombre === '') { echo json_encode(['ok' => false, 'msg' => 'Faltan datos.']); break; }
        $id = guardarPeriodoAcademico($idConfig, $idPeriodo, $nombre, $tipo, $fechaInicio, $fechaFin, $orden, $visible, $bloqueado, $idPeriodoRecuperaDe);
        echo json_encode($id ? ['ok' => true, 'idPeriodo' => $id] : ['ok' => false, 'msg' => 'No se pudo guardar.']);
        break;

    case 'eliminar_periodo':
        $idPeriodo = (int)($_POST['idPeriodo'] ?? 0);
        echo json_encode(['ok' => ($idConfig && $idPeriodo) ? eliminarPeriodoAcademico($idConfig, $idPeriodo) : false]);
        break;

    case 'guardar_tipo':
        $idTipo = (int)($_POST['idTipo'] ?? 0) ?: null;
        $nombre = trim($_POST['nombre'] ?? '');
        $notaMaxima = (float)($_POST['notaMaxima'] ?? 10);
        $peso = (float)($_POST['peso'] ?? 1);
        $obligatorio = !empty($_POST['obligatorio']);
        $incluirEnMedia = !empty($_POST['incluirEnMedia']);
        $origen = in_array($_POST['origen'] ?? '', ['examen','reto','ra_ce','fct','tfg','otro'], true) ? $_POST['origen'] : 'otro';
        $orden = (int)($_POST['orden'] ?? 1);
        if (!$idConfig || $nombre === '') { echo json_encode(['ok' => false, 'msg' => 'Faltan datos.']); break; }
        $id = guardarTipoEvaluacion($idConfig, $idTipo, $nombre, $notaMaxima, $peso, $obligatorio, false, $incluirEnMedia, $origen, $orden);
        echo json_encode($id ? ['ok' => true, 'idTipo' => $id] : ['ok' => false, 'msg' => 'No se pudo guardar.']);
        break;

    case 'eliminar_tipo':
        $idTipo = (int)($_POST['idTipo'] ?? 0);
        echo json_encode(['ok' => ($idConfig && $idTipo) ? eliminarTipoEvaluacion($idConfig, $idTipo) : false]);
        break;

    case 'guardar_calificacion':
        if (!$idConfig) { echo json_encode(['ok' => false, 'msg' => 'Falta la configuración.']); break; }
        // Escala 0-10, aprobado=5 y nota final entera (0 decimales) no son
        // configurables desde el formulario — los fija la normativa de FP
        // española (ver vistas/admin/academico/configuracionAcademica.php).
        $ok = actualizarPoliticaCalificacion(
            $idConfig,
            0, 10, 5, 0,
            (float)($_POST['pesoTfgEnMedia'] ?? 1)
        ) && actualizarReglasPromocion(
            $idConfig,
            !empty($_POST['requiereTodosModulos']), (float)($_POST['notaMinimaGlobal'] ?? 5),
            (int)($_POST['permiteModulosPendientes'] ?? 0)
        );
        echo json_encode(['ok' => $ok]);
        break;

    case 'guardar_fct':
        if (!$idConfig) { echo json_encode(['ok' => false, 'msg' => 'Falta la configuración.']); break; }
        $metodo = in_array($_POST['metodoEvaluacion'] ?? '', ['nota','apto_no_apto','ambos'], true) ? $_POST['metodoEvaluacion'] : 'ambos';
        $ok = actualizarConfigFCT(
            $idConfig, !empty($_POST['habilitado']), (int)($_POST['horasRequeridasDefecto'] ?? 0),
            $metodo, (float)($_POST['pesoEnMedia'] ?? 0), !empty($_POST['requiereAprobarParaTitular'])
        );
        echo json_encode(['ok' => $ok]);
        break;

    case 'guardar_tfg':
        if (!$idConfig) { echo json_encode(['ok' => false, 'msg' => 'Falta la configuración.']); break; }
        $ok = actualizarConfigTFG(
            $idConfig, !empty($_POST['habilitado']), false, false,
            (float)($_POST['notaMinima'] ?? 5), (float)($_POST['pesoEnMedia'] ?? 1), false
        );
        echo json_encode(['ok' => $ok]);
        break;

    case 'guardar_retos':
        if (!$idConfig) { echo json_encode(['ok' => false, 'msg' => 'Falta la configuración.']); break; }
        $ok = actualizarConfigRetos(
            $idConfig, (float)($_POST['pesoDefecto'] ?? 1), false,
            false, false, false
        );
        echo json_encode(['ok' => $ok]);
        break;

    case 'aplicar_plantilla':
        $idPlantilla = (int)($_POST['idPlantilla'] ?? 0);
        $nombreNueva = trim($_POST['nombre'] ?? '') ?: 'Configuración desde plantilla';
        if (!$idPlantilla) { echo json_encode(['ok' => false, 'msg' => 'Selecciona una plantilla.']); break; }
        $id = aplicarPlantillaAcademica($idPlantilla, $nombreNueva);
        echo json_encode($id ? ['ok' => true, 'idConfig' => $id] : ['ok' => false, 'msg' => 'No se pudo aplicar la plantilla.']);
        break;

    case 'guardar_como_plantilla':
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        if (!$idConfig || $nombre === '') { echo json_encode(['ok' => false, 'msg' => 'Faltan datos.']); break; }
        $snapshot = exportarConfigComoArray($idConfig);
        $id = guardarPlantillaAcademica($nombre, $descripcion, $snapshot, true);
        echo json_encode($id ? ['ok' => true, 'idPlantilla' => $id] : ['ok' => false, 'msg' => 'No se pudo guardar la plantilla.']);
        break;

    case 'activar':
        if (!$idConfig) { echo json_encode(['ok' => false, 'msg' => 'Falta la configuración.']); break; }
        $ok = activarConfigAcademica($idConfig);
        if ($ok) {
            actualizarFeatureToggle('feature_academico_config', 1);
            registrarAccion('actualizar', 'academico_config', $idConfig, 'Motor académico configurable activado');
        }
        echo json_encode(['ok' => $ok]);
        break;

    default:
        echo json_encode(['ok' => false, 'msg' => 'Acción no reconocida.']);
}
