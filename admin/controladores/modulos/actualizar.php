<?php
session_start();
require_once "../../../modelos/modulos.php";

if (isset($_POST['guardarModulo'])) {
    $id = $_POST['idModulo'];
    $nombre = trim($_POST['nombreModulo']);
    $idCiclo = $_POST['idCiclo'];
    $horas = $_POST['horasMaximas'];

    if (empty($id)) {
        header("Location: ../../vistas/modulos/verModulos.php");
    } else if (empty($nombre)) {
        $_SESSION['error'] = "El nombre del módulo es obligatorio.";
        header("Location: ../../vistas/modulos/modificarModulos.php?idModulo=$id");
    } else if (empty($idCiclo)) {
        $_SESSION['error'] = "El ciclo asociado es obligatorio.";
        header("Location: ../../vistas/modulos/modificarModulos.php?idModulo=$id");
    } else if (!empty($horas) && !is_numeric($horas)) {
        $_SESSION['error'] = "Las horas deben ser un valor numérico.";
        header("Location: ../../vistas/modulos/modificarModulos.php?idModulo=$id");
    } else {
        if (actualizarModulo($id, $nombre, $idCiclo, $horas)) {
            $_SESSION['exito'] = "Módulo actualizado correctamente.";
            header("Location: ../../vistas/modulos/verModulos.php");
        } else {
            $_SESSION['error'] = "Error al actualizar el módulo.";
            header("Location: ../../vistas/modulos/modificarModulos.php?idModulo=$id");
        }
    }
    exit;
}

header("Location: ../../vistas/modulos/verModulos.php");
exit;
?>