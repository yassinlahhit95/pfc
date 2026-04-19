<?php
session_start();
require_once "../../modelos/modulos.php";

if (isset($_POST['guardarModulo'])) {
    $id = $_POST['idModulo'];
    $nombre = trim($_POST['nombreModulo'] ?? '');
    $idCiclo = $_POST['idCiclo'] ?? '';
    $horas = $_POST['horasMaximas'] ?? 0;

    if (empty($id)) {
        header("Location: ../../vistas/modulos/verModulos.php");
        exit;
    }

    if (empty($nombre) || empty($idCiclo)) {
        $_SESSION['error'] = "El nombre y el ciclo son obligatorios.";
        header("Location: ../../vistas/modulos/modificarModulos.php?idModulo=$id");
        exit;
    }

    if (actualizarModulo($id, $nombre, $idCiclo, $horas)) {
        $_SESSION['exito'] = "Módulo actualizado correctamente.";
        header("Location: ../../vistas/modulos/verModulos.php");
    } else {
        $_SESSION['error'] = "Error al actualizar el módulo.";
        header("Location: ../../vistas/modulos/modificarModulos.php?idModulo=$id");
    }
    exit;
}

header("Location: ../../vistas/modulos/verModulos.php");
exit;
?>
