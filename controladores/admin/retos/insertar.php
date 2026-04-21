<?php
session_start();
require_once "../../../modelos/retos.php";

if (isset($_POST['insertarReto'])) {
    $nombre = trim($_POST['nombreReto']);
    $fInicio = $_POST['fechaInicio'];
    $fFin = $_POST['fechaFin'];
    $horas = $_POST['horasReto'];
    $listaModulos = $_POST['modulos'];

    $regexFecha = "/^\d{4}-\d{2}-\d{2}$/";

    if (empty($nombre)) {
        $_SESSION['error'] = "El nombre del reto es obligatorio.";
        header("Location: /pfc/vistas/admin/retos/agregarRetos.php");
    } else if (empty($fInicio)) {
        $_SESSION['error'] = "La fecha de inicio es obligatoria.";
        header("Location: /pfc/vistas/admin/retos/agregarRetos.php");
    } else if (!preg_match($regexFecha, $fInicio)) {
        $_SESSION['error'] = "La fecha de inicio no es válida.";
        header("Location: /pfc/vistas/admin/retos/agregarRetos.php");
    } else if (empty($fFin)) {
        $_SESSION['error'] = "La fecha de fin es obligatoria.";
        header("Location: /pfc/vistas/admin/retos/agregarRetos.php");
    } else if (!preg_match($regexFecha, $fFin)) {
        $_SESSION['error'] = "La fecha de fin no es válida.";
        header("Location: /pfc/vistas/admin/retos/agregarRetos.php");
    } else if (!empty($horas) && !is_numeric($horas)) {
        $_SESSION['error'] = "Las horas deben ser un valor numérico.";
        header("Location: /pfc/vistas/admin/retos/agregarRetos.php");
    } else {
        $idReto = insertarReto($nombre, $fInicio, $fFin, $horas);
        if ($idReto) {
            if (isset($listaModulos) && is_array($listaModulos)) {
                foreach ($listaModulos as $idMod) {
                    asociarModuloReto($idMod, $idReto);
                }
            }
            $_SESSION['exito'] = "Reto creado correctamente.";
            header("Location: /pfc/vistas/admin/retos/verRetos.php");
        } else {
            $_SESSION['error'] = "Error al crear el reto.";
            header("Location: /pfc/vistas/admin/retos/agregarRetos.php");
        }
    }
    exit;
}

header("Location: /pfc/vistas/admin/retos/verRetos.php");
exit;
?>