<?php
session_start();
require_once "../../../modelos/retos.php";

if (isset($_POST['actualizarReto'])) {
    $idReto = trim($_POST['idReto']);
    $nom = trim($_POST['nombreReto']);
    $fIni = trim($_POST['fechaInicio']);
    $fFin = trim($_POST['fechaFin']);
    $hrs = trim($_POST['horasReto']);
    $mods = $_POST['modulos'] ?? [];

    $errs = [];

    if (empty($idReto)) {
        header("Location: ../../../vistas/profesores/retos/lista.php");
        exit;
    }

    if (empty($nom)) $errs['nombreReto'] = "El nombre del reto es obligatorio.";
    if (empty($fIni)) $errs['fechaInicio'] = "La fecha de inicio es obligatoria.";
    if (empty($fFin)) $errs['fechaFin'] = "La fecha de fin es obligatoria.";
    if (empty($hrs)) {
        $errs['horasReto'] = "Las horas son obligatorias.";
    } elseif (!is_numeric($hrs)) {
        $errs['horasReto'] = "Las horas deben ser un valor numérico.";
    }

    if (empty($mods)) {
        $errs['modulos'] = "Debe seleccionar al menos un módulo para este reto.";
    }

    if (!empty($errs)) {
        $_SESSION['errores'] = $errs;
        header("Location: ../../../vistas/profesores/retos/editar.php?id=$idReto");
        exit;
    }

    $res = actualizarReto($idReto, $nom, $fIni, $fFin, $hrs, $mods);
    if ($res) {
        $_SESSION['exito'] = "Reto actualizado correctamente.";
        header("Location: ../../../vistas/profesores/retos/lista.php");
        exit;
    } else {
        $_SESSION['error'] = "Error al actualizar el reto.";
        header("Location: ../../../vistas/profesores/retos/editar.php?id=$idReto");
        exit;
    }
}

header("Location: ../../../vistas/profesores/retos/lista.php");
exit;
?>
