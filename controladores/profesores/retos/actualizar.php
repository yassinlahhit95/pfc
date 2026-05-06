<?php
session_start();
require_once "../../../modelos/retos.php";

if (isset($_POST['actualizarReto'])) {
    $idReto = trim($_POST['idReto']);
    $nom = trim($_POST['nombreReto']);
    $fIni = trim($_POST['fechaInicio']);
    $fFin = trim($_POST['fechaFin']);
    $hrs = trim($_POST['horasReto']);

    $errs = [];

    if (empty($idReto)) {
        header("Location: ../../../vistas/profesores/retos/lista.php");
        exit;
    }

    if (empty($nom)) $errs['nombreReto'] = "El nombre del reto es obligatorio.";
    if (empty($fIni)) $errs['fechaInicio'] = "La fecha de inicio es obligatoria.";
    if (empty($fFin)) $errs['fechaFin'] = "La fecha de fin es obligatoria.";
    if (empty($hrs)) $errs['horasReto'] = "Las horas son obligatorias.";

    if (!empty($errs)) {
        $_SESSION['errores'] = $errs;
        header("Location: ../../../vistas/profesores/retos/editar.php?id=$idReto");
        exit;
    }

    $res = actualizarReto($idReto, $nom, $fIni, $fFin, $hrs);
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
