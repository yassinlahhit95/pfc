<?php
session_start();
require_once "../../../modelos/ciclos.php";

if (isset($_POST['guardarCiclo'])) {
    $nombre = trim($_POST['nombreCiclo']);
    $descripcion = trim($_POST['descripcionCiclo']);
    $idNivel = $_POST['idNivel'];
    $listaProfesores = $_POST['profesores'];
    $listaAulas = $_POST['aulas'];

    if (empty($nombre)) {
        $_SESSION['error'] = "El nombre del ciclo es obligatorio.";
        header("Location: ../../vistas/ciclos/agregarCiclos.php");
    } else if (empty($descripcion)) {
        $_SESSION['error'] = "La descripción del ciclo es obligatoria.";
        header("Location: ../../vistas/ciclos/agregarCiclos.php");
    } else {
        if (comprobarNombreRepetido($nombre)) {
            $_SESSION['error'] = "Ese nombre de ciclo ya está registrado.";
            header("Location: ../../vistas/ciclos/agregarCiclos.php");
        } else {
            if (insertarNuevoCiclo($nombre, $descripcion, $idNivel, $listaProfesores, $listaAulas)) {
                $_SESSION['exito'] = "Ciclo creado con éxito.";
                header("Location: ../../vistas/ciclos/verCiclos.php");
            } else {
                $_SESSION['error'] = "Error al guardar el ciclo en la base de datos.";
                header("Location: ../../vistas/ciclos/agregarCiclos.php");
            }
        }
    }
    exit;
}

header("Location: ../../vistas/ciclos/verCiclos.php");
exit;
?>