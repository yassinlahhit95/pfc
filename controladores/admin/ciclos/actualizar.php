<?php
session_start();
require_once "../../../modelos/ciclos.php";
if (isset($_POST['guardarCiclo'])) {
    $id = $_POST['idCiclo'];
    $nombre = $_POST['nombreCiclo'];
    $descripcion = $_POST['descripcionCiclo'];
    $idNivel = $_POST['idNivel'];
    $listaProfesores = $_POST['profesores'];
    $listaAulas = $_POST['aulas'];
    if (empty($id)) {
        $_SESSION['error'] = "ID obligatorio";
    } else if (empty($nombre)) {
        $_SESSION['error'] = "Nombre obligatorio";
    } else if (empty($descripcion)) {
        $_SESSION['error'] = "Descripción obligatoria";
    } else if (comprobarNombreEnOtroCiclo($nombre, $id)) {
        $_SESSION['error'] = "Nombre repetido";
    } else if (actualizarCicloExistente($id, $nombre, $descripcion, $idNivel, $listaProfesores, $listaAulas)) {
        $_SESSION['exito'] = "Ok";
        header("Location: /pfc/vistas/admin/ciclos/verCiclos.php");
        exit;
    } else {
        $_SESSION['error'] = "Error BD";
    }
    header("Location: /pfc/vistas/admin/ciclos/modificarCiclos.php?idCiclo=$id");
    exit;
}
header("Location: /pfc/vistas/admin/ciclos/verCiclos.php");
exit;

