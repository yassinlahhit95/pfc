<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/aulas.php";
require_once __DIR__ . "/../../../modelos/log.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['guardarAula'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/admin/aulas/agregarAula.php");
        exit;
    }
    $planta    = (int)($_POST['planta'] ?? -1);
    $numero    = (int)($_POST['numero'] ?? 0);
    $nombre    = trim($_POST['nombreAula'] ?? '');
    $tipo      = $_POST['tipoAula'] ?? 'teoria';
    $capacidad = ($_POST['capacidad'] ?? '') !== '' ? (int)$_POST['capacidad'] : null;
    $activa    = isset($_POST['activa']) ? 1 : 0;
    $nombre    = $nombre !== '' ? $nombre : null;

    $tiposValidos = ['teoria', 'laboratorio', 'taller', 'otro'];

    // ── Validación ──
    $errores = '';
    if ($planta < 0 || $planta > 5)            $errores = "La planta seleccionada debe estar comprendida entre 0 y 5.";
    elseif ($numero < 1 || $numero > 99)       $errores = "El número de aula debe ser un valor numérico comprendido entre 1 y 99.";
    elseif (!in_array($tipo, $tiposValidos))   $errores = "El tipo de aula seleccionado no es válido.";
    elseif (checkAulaExistente($planta, $numero)) $errores = "Ya existe un aula registrada en la misma planta y con el mismo número.";

    if ($errores) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_aula'] = $_POST;
        header("Location: ../../../vistas/admin/aulas/agregarAula.php");
        exit;
    }

    if (insertarAula($planta, $numero, $nombre, $tipo, $capacidad, $activa)) {
        registrarAccion('insertar', 'aulas', null, "Planta $planta · Nº$numero · $tipo");
        $_SESSION['exito'] = "El aula ha sido registrada correctamente.";
        header("Location: ../../../vistas/admin/aulas/gestionAulas.php");
        exit;
    }
    $_SESSION['errores'] = "Ocurrió un error al intentar registrar la nueva aula.";
    $_SESSION['datos_aula'] = $_POST;
    header("Location: ../../../vistas/admin/aulas/agregarAula.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/admin/aulas/gestionAulas.php");
exit;
