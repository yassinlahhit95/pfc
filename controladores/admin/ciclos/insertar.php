<?php
session_start();
require_once "../../../modelos/ciclos.php";
if (isset($_POST['guardarCiclo'])) {
    $nombre = $_POST['nombreCiclo'];
    $descripcion = $_POST['descripcionCiclo'];
    $idNivel = $_POST['idNivel'];
    $listaProfesores = $_POST['profesores'];
    $listaAulas = $_POST['aulas'];
    if (empty($nombre)) {
        $_SESSION['error'] = "Nombre obligatorio";
    } else if (empty($descripcion)) {
        $_SESSION['error'] = "Descripción obligatoria";
    } else if (comprobarNombreRepetido($nombre)) {
        $_SESSION['error'] = "Nombre repetido";
    } else if (insertarNuevoCiclo($nombre, $descripcion, $idNivel, $listaProfesores, $listaAulas)) {
        $_SESSION['exito'] = "Ok";
        header("Location: /pfc/vistas/admin/ciclos/verCiclos.php");
        exit;
    } else {
        $_SESSION['error'] = "Error BD";
    }
    header("Location: /pfc/vistas/admin/ciclos/agregarCiclos.php");
    exit;
}
header("Location: /pfc/vistas/admin/ciclos/verCiclos.php");
exit;

