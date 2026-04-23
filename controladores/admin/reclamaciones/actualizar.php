<?php
session_start();
require_once "../../../modelos/reclamaciones.php";

if (isset($_POST['actualizarReclamacion'])) {
    $id_reclamacion = $_POST['idReclamacion'];
    $asunto = trim($_POST['asuntoReclamacion']);
    $descripcion = trim($_POST['descripcionReclamacion']);
    $estado = $_POST['estadoReclamacion'];

    $lista_de_errores = [];

    if (empty($asunto)) {
        $lista_de_errores['asuntoReclamacion'] = "El asunto es obligatorio.";
    }
    if (empty($descripcion)) {
        $lista_de_errores['descripcionReclamacion'] = "La descripción es obligatoria.";
    }

    if (empty($lista_de_errores)) {
        $resultado = actualizarReclamacion($id_reclamacion, $asunto, $descripcion, $estado);
        if ($resultado) {
            $_SESSION['exito'] = "Reclamación actualizada correctamente.";
            header("Location: /pfc/vistas/admin/reclamaciones/verReclamaciones.php");
            exit;
        } else {
            $_SESSION['error'] = "Error al actualizar en la base de datos.";
        }
    } else {
        $_SESSION['errores'] = $lista_de_errores;
        $_SESSION['datos_reclamacion'] = $_POST;
    }

    header("Location: /pfc/vistas/admin/reclamaciones/modificarReclamacion.php?idReclamacion=$id_reclamacion");
    exit;
}

header("Location: /pfc/vistas/admin/reclamaciones/verReclamaciones.php");
exit;
