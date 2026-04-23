<?php
session_start();
require_once "../../../modelos/reclamaciones.php";

if (isset($_POST['guardarReclamacion'])) {
    $id_estudiante = $_POST['idEstudiante'];
    $asunto = trim($_POST['asuntoReclamacion']);
    $descripcion = trim($_POST['descripcionReclamacion']);

    $lista_de_errores = [];

    if (empty($id_estudiante)) {
        $lista_de_errores['idEstudiante'] = "Debe seleccionar un estudiante.";
    }
    if (empty($asunto)) {
        $lista_de_errores['asuntoReclamacion'] = "El asunto es obligatorio.";
    }
    if (empty($descripcion)) {
        $lista_de_errores['descripcionReclamacion'] = "La descripción es obligatoria.";
    }

    if (empty($lista_de_errores)) {
        $resultado = insertarReclamacion($id_estudiante, $asunto, $descripcion);
        if ($resultado) {
            $_SESSION['exito'] = "Reclamación registrada correctamente.";
            header("Location: /pfc/vistas/admin/reclamaciones/verReclamaciones.php");
            exit;
        } else {
            $_SESSION['error'] = "Error al guardar en la base de datos.";
        }
    } else {
        $_SESSION['errores'] = $lista_de_errores;
        $_SESSION['datos_reclamacion'] = $_POST;
    }

    header("Location: /pfc/vistas/admin/reclamaciones/verReclamaciones.php");
    exit;
}

header("Location: /pfc/vistas/admin/reclamaciones/verReclamaciones.php");
exit;
