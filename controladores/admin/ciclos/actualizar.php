<?php
session_start();
require_once "../../../modelos/ciclos.php";

if (isset($_POST['actualizarCiclo'])) {
    $id_ciclo = $_POST['idCiclo'];
    $nombre = trim($_POST['nombreCiclo']);
    $idNivel = $_POST['idNivel'];
    $descripcion = trim($_POST['descripcionCiclo']);
    $precio = $_POST['precioCiclo'];
    $profesores = isset($_POST['profesores']) ? $_POST['profesores'] : [];
    $aulas = isset($_POST['aulas']) ? $_POST['aulas'] : [];

    $lista_de_errores = [];

    if (empty($nombre)) {
        $lista_de_errores['nombreCiclo'] = "El nombre del ciclo es obligatorio.";
    }
    
    if (empty($idNivel)) {
        $lista_de_errores['idNivel'] = "El nivel es obligatorio.";
    }

    if (empty($descripcion)) {
        $lista_de_errores['descripcionCiclo'] = "La descripción es obligatoria.";
    }

    if (empty($lista_de_errores)) {
        $resultado = actualizarCicloExistente($id_ciclo, $nombre, $descripcion, $idNivel, $profesores, $aulas, $precio);
        if ($resultado) {
            $_SESSION['exito'] = "Ciclo actualizado correctamente.";
            header("Location: /pfc/vistas/admin/ciclos/verCiclos.php");
            exit;
        } else {
            $_SESSION['error'] = "Error al actualizar en la base de datos.";
        }
    } else {
        $_SESSION['errores'] = $lista_de_errores;
        $_SESSION['datos_ciclos'] = $_POST;
    }

    header("Location: /pfc/vistas/admin/ciclos/modificarCiclos.php?idCiclo=$id_ciclo");
    exit;
}

header("Location: /pfc/vistas/admin/ciclos/verCiclos.php");
exit;
