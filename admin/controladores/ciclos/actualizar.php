<?php
session_start();
require_once "../../modelos/ciclos.php";

if (isset($_POST['guardarCiclo'])) {
    $id = $_POST['idCiclo'];
    $nombre = trim($_POST['nombreCiclo'] ?? '');
    $descripcion = trim($_POST['descripcionCiclo'] ?? '');
    $idNivel = $_POST['idNivel'] ?? '';
    $idEstado = $_POST['idEstado'] ?? 1;
    $listaProfesores = $_POST['profesores'] ?? [];
    $listaAulas = $_POST['aulas'] ?? [];

    if (empty($id)) {
        header("Location: ../../vistas/ciclos/verCiclos.php");
        exit;
    }

    if (empty($nombre)) {
        $_SESSION['error'] = "El nombre es obligatorio.";
        header("Location: ../../vistas/ciclos/modificarCiclos.php?idCiclo=$id");
        exit;
    }

    if (comprobarNombreEnOtroCiclo($nombre, $id)) {
        $_SESSION['error'] = "Ese nombre ya está siendo usado por otro ciclo.";
        header("Location: ../../vistas/ciclos/modificarCiclos.php?idCiclo=$id");
        exit;
    }

    if (actualizarCicloExistente($id, $nombre, $descripcion, $idNivel, $idEstado, $listaProfesores, $listaAulas)) {
        $_SESSION['exito'] = "Ciclo actualizado correctamente.";
        header("Location: ../../vistas/ciclos/verCiclos.php");
    } else {
        $_SESSION['error'] = "Error al actualizar el ciclo.";
        header("Location: ../../vistas/ciclos/modificarCiclos.php?idCiclo=$id");
    }
    exit;
}

header("Location: ../../vistas/ciclos/verCiclos.php");
exit;
?>
