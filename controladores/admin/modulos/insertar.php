<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . "/../../../modelos/modulos.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['guardarModulo'])) {
    $nombre       = trim($_POST['nombreModulo']);
    $idCiclo      = (int)($_POST['idCiclo'] ?? 0);
    $horasMaximas = trim($_POST['horasMaximas']);

    $avisos = [];
    if (empty($nombre))      $avisos['nombreModulo'] = "El nombre del módulo es un campo obligatorio.";
    if (empty($idCiclo))     $avisos['idCiclo'] = "Debe seleccionar un ciclo formativo.";
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

    if (insertarModulo($nombre, $idCiclo, $horasMaximas)) {
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
