<?php
session_start();
require_once "../../../modelos/ciclos.php";

if (isset($_POST['guardarCiclo'])) {
    $id = $_POST['idCiclo'];
    $nombre = trim($_POST['nombreCiclo']);
    $descripcion = trim($_POST['descripcionCiclo']);
    $idNivel = $_POST['idNivel'];
    $listaProfesores = $_POST['profesores'];
    $listaAulas = $_POST['aulas'];

    if (empty($id)) {
        header("Location: /pfc/vistas/admin/ciclos/verCiclos.php");
    } else if (empty($nombre)) {
        $_SESSION['error'] = "El nombre es obligatorio.";
        header("Location: /pfc/vistas/admin/ciclos/modificarCiclos.php?idCiclo=$id");
    } else if (empty($descripcion)) {
        $_SESSION['error'] = "La descripción es obligatoria.";
        header("Location: /pfc/vistas/admin/ciclos/modificarCiclos.php?idCiclo=$id");
    } else {
        if (comprobarNombreEnOtroCiclo($nombre, $id)) {
            $_SESSION['error'] = "Ese nombre ya está siendo usado por otro ciclo.";
            header("Location: /pfc/vistas/admin/ciclos/modificarCiclos.php?idCiclo=$id");
        } else {
            if (actualizarCicloExistente($id, $nombre, $descripcion, $idNivel, $listaProfesores, $listaAulas)) {
                $_SESSION['exito'] = "Ciclo actualizado correctamente.";
                header("Location: /pfc/vistas/admin/ciclos/verCiclos.php");
            } else {
                $_SESSION['error'] = "Error al actualizar el ciclo.";
                header("Location: /pfc/vistas/admin/ciclos/modificarCiclos.php?idCiclo=$id");
            }
        }
    }
    exit;
}

header("Location: /pfc/vistas/admin/ciclos/verCiclos.php");
exit;
?>