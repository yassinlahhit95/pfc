<?php
session_start();
require_once "../../../modelos/ciclos.php";

if (isset($_POST['guardarCiclo'])) {
    $nombre = $_POST['nombreCiclo'];
    $grado = $_POST['gradoCiclo'];

    $lista_de_errores = [];

    if (empty($nombre)) {
        $lista_de_errores['nombreCiclo'] = "El nombre del ciclo es obligatorio.";
    }
    
    if (empty($grado)) {
        $lista_de_errores['gradoCiclo'] = "El grado es obligatorio.";
    }

    if (empty($lista_de_errores)) {
        $resultado = insertarCiclo($nombre, $grado);
        if ($resultado) {
            $_SESSION['exito'] = "Ciclo registrado correctamente.";
            header("Location: /pfc/vistas/admin/ciclos/verCiclos.php");
            exit;
        } else {
            $_SESSION['error'] = "Error al insertar en la base de datos.";
        }
    } else {
        $_SESSION['errores'] = $lista_de_errores;
        $_SESSION['datos_ciclos'] = $_POST;
    }

    header("Location: /pfc/vistas/admin/ciclos/verCiclos.php");
    exit;
}

header("Location: /pfc/vistas/admin/ciclos/verCiclos.php");
exit;
