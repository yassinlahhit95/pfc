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
if (isset($_POST['actualizarAula'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/admin/aulas/gestionAulas.php");
        exit;
    }
    $idAula    = (int)($_POST['idAula'] ?? 0);
    $planta    = (int)($_POST['planta'] ?? -1);
    $numero    = (int)($_POST['numero'] ?? 0);
    $nombre    = trim($_POST['nombreAula'] ?? '');
    $tipo      = $_POST['tipoAula'] ?? 'teoria';
    $capacidad = ($_POST['capacidad'] ?? '') !== '' ? (int)$_POST['capacidad'] : null;
    $activa    = isset($_POST['activa']) ? 1 : 0;
    $nombre    = $nombre !== '' ? $nombre : null;

    $tiposValidos = ['teoria', 'laboratorio', 'taller', 'otro'];

    // ── Validación ──
    if ($idAula <= 0) {
        $_SESSION['errores'] = "El identificador del aula no es válido.";
        header("Location: ../../../vistas/admin/aulas/gestionAulas.php");
        exit;
    }
    $errores = [];
    if ($planta < 0 || $planta > 5)            $errores['planta'] = "La planta seleccionada debe estar comprendida entre 0 y 5.";
    if ($numero < 1 || $numero > 99)           $errores['numero'] = "El número de aula debe ser un valor numérico comprendido entre 1 y 99.";
    if (!in_array($tipo, $tiposValidos))       $errores['tipoAula'] = "El tipo de aula seleccionado no es válido.";
    if (empty($errores) && checkAulaExistente($planta, $numero, $idAula)) $errores['numero'] = "Ya existe otra aula registrada en la misma planta y con el mismo número.";

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_aula'] = $_POST;
        header("Location: ../../../vistas/admin/aulas/modificarAula.php?id=" . $idAula);
        exit;
    }

    if (actualizarAula($idAula, $planta, $numero, $nombre, $tipo, $capacidad, $activa)) {
        registrarAccion('actualizar', 'aulas', $idAula, "Planta $planta · Nº$numero · $tipo");
        $_SESSION['exito'] = "La información del aula ha sido actualizada correctamente.";
        header("Location: ../../../vistas/admin/aulas/gestionAulas.php");
        exit;
    }
    $_SESSION['errores'] = "Ocurrió un error al intentar actualizar la información del aula.";
    $_SESSION['datos_aula'] = $_POST;
    header("Location: ../../../vistas/admin/aulas/modificarAula.php?id=" . $idAula);
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/admin/aulas/gestionAulas.php");
exit;
