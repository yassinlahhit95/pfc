<?php
session_start();
require_once __DIR__ . "/../../../modelos/aulas.php";

if (isset($_POST['guardarAula'])) {
    $nombreAula = trim($_POST['nombreAula']);

    $hayError = false;
    if (empty($nombreAula)) {
        $hayError = true;
        $_SESSION['error'] = "El nombre del aula es obligatorio.";
    }

    if (!$hayError) {
        $resultado = insertarAula($nombreAula);
        if ($resultado) {
            $_SESSION['exito'] = "Aula registrada.";
            header("Location: ../../../vistas/admin/aulas/verAulas.php");
            exit;
        } else {
            $_SESSION['error'] = "Error al insertar el aula en la base de datos.";
        }
    } else {
        $_SESSION['datos_aulas'] = $_POST;
    }

    header("Location: ../../../vistas/admin/aulas/verAulas.php");
    exit;
}

header("Location: ../../../vistas/admin/aulas/verAulas.php");
exit;
