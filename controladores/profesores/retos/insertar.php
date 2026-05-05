<?php
session_start();
require_once "../../../modelos/retos.php";

if (isset($_POST['insertarReto'])) {
    $nom = trim($_POST['nombreReto']);
    $fIni = trim($_POST['fechaInicio']);
    $fFin = trim($_POST['fechaFin']);
    $hrs = trim($_POST['horasReto']);
    $mods = $_POST['modulos'] ?? [];

    $errs = [];

    if (empty($nom)) $errs['nombreReto'] = "El nombre del reto es obligatorio.";
    if (empty($fIni)) $errs['fechaInicio'] = "La fecha de inicio es obligatoria.";
    if (empty($fFin)) $errs['fechaFin'] = "La fecha de fin es obligatoria.";
    if (empty($hrs)) $errs['horasReto'] = "Las horas son obligatorias.";

    if (!empty($errs)) {
        $_SESSION['errores'] = $errs;
        $_SESSION['datos_reto'] = $_POST;
        header("Location: ../../../vistas/profesores/retos/agregar.php");
        exit;
    }

    $res = insertarReto($nom, $fIni, $fFin, $hrs, $mods);
    if ($res) {
        $_SESSION['exito'] = "Reto insertado correctamente.";
        header("Location: ../../../vistas/profesores/retos/lista.php");
        exit;
    } else {
        $_SESSION['error'] = "Error al insertar el reto.";
        $_SESSION['datos_reto'] = $_POST;
        header("Location: ../../../vistas/profesores/retos/agregar.php");
        exit;
    }
}

header("Location: ../../../vistas/profesores/retos/lista.php");
exit;
