<?php
session_start();
require_once __DIR__ . "/../../../modelos/retos.php";

if (isset($_POST['insertarReto'])) {
    $nombreReto = trim($_POST['nombreReto']);
    $fechaInicio = trim($_POST['fechaInicio']);
    $fechaFin = trim($_POST['fechaFin']);
    $horasReto = trim($_POST['horasReto']);
    $listaDeModulos = isset($_POST['modulos']) ? $_POST['modulos'] : [];

    $hayError = false;

    if (empty($nombreReto)) {
        $_SESSION['error'] = "El nombre del reto es obligatorio.";
        $hayError = true;
    }

    if (!$hayError) {
        $resultado = insertarReto($nombreReto, $fechaInicio, $fechaFin, $horasReto, $listaDeModulos);
        if ($resultado) {
            $_SESSION['exito'] = "Reto insertado.";
            header("Location: ../../../vistas/profesores/retos/lista.php");
            exit;
        } else {
            $_SESSION['error'] = "Error al insertar el reto.";
        }
    }

    header("Location: ../../../vistas/profesores/retos/agregar.php");
    exit;
}

header("Location: ../../../vistas/profesores/retos/lista.php");
exit;
