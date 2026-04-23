<?php
session_start();
require_once "../../../modelos/aulas.php";

if (isset($_POST['actualizarAula'])) {
    $id_aula = $_POST['idAula'];
    $nombre_aula = trim($_POST['nombreAula']);

    $lista_de_errores = array();
    if (empty($nombre_aula)) {
        $lista_de_errores['nombreAula'] = "El nombre del aula no puede estar vacío.";
    }

    if (empty($lista_de_errores)) {
        $resultado = actualizarAula($id_aula, $nombre_aula);
        if ($resultado) {
            $_SESSION['exito'] = "Aula actualizada correctamente.";
            header("Location: /pfc/vistas/admin/aulas/verAulas.php");
            exit;
        } else {
            $_SESSION['error'] = "Error al actualizar el aula.";
        }
    } else {
        $_SESSION['errores'] = $lista_de_errores;
        $_SESSION['datos_aulas'] = $_POST;
    }

    header("Location: /pfc/vistas/admin/aulas/modificarAulas.php?idAula=$id_aula");
    exit;
}

header("Location: /pfc/vistas/admin/aulas/verAulas.php");
exit;
