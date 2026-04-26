<?php
session_start();
require_once "../../../modelos/aulas.php";

if (isset($_POST['guardarAula'])) {
    $nombre_aula = $_POST['nombreAula'];

    $lista_de_errores = array();
    if (empty($nombre_aula)) {
        $lista_de_errores['nombreAula'] = "El nombre del aula es obligatorio.";
    }

    if (empty($lista_de_errores)) {
        $resultado = insertarAula($nombre_aula);
        if ($resultado) {
            $_SESSION['exito'] = "Aula registrada correctamente.";
            header("Location: /pfc/vistas/admin/aulas/verAulas.php");
            exit;
        } else {
            $_SESSION['error'] = "Error al insertar el aula en la base de datos.";
        }
    } else {
        $_SESSION['errores'] = $lista_de_errores;
        $_SESSION['datos_aulas'] = $_POST;
    }

    header("Location: /pfc/vistas/admin/aulas/verAulas.php");
    exit;
}

header("Location: /pfc/vistas/admin/aulas/verAulas.php");
exit;

