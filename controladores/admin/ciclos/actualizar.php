<?php
session_start();
require_once "../../../modelos/ciclos.php";

if (isset($_POST['actualizarCiclo'])) {
    $id = $_POST['idCiclo'];
    $nombre = trim($_POST['nombreCiclo']);
    $abreviatura = trim($_POST['abreviaturaCiclo']);
    $idNivel = $_POST['idNivel'];
    $precio = $_POST['precioCiclo'];
    
    $profesores = array();
    if (isset($_POST['profesores'])) { $profesores = $_POST['profesores']; }
    
    $aulas = array();
    if (isset($_POST['aulas'])) { $aulas = $_POST['aulas']; }

    $lista_de_errores = array();

    if (empty($nombre)) {
        $lista_de_errores['nombreCiclo'] = "El nombre del ciclo es obligatorio.";
    }
    
    if (empty($abreviatura)) {
        $lista_de_errores['abreviaturaCiclo'] = "La abreviatura es obligatoria.";
    }

    if (empty($lista_de_errores)) {
        // Signature updated: actualizarCicloExistente($id, $nombre, $abreviatura, $idNivel, $profesores, $aulas, $precio)
        $resultado = actualizarCicloExistente($id, $nombre, $abreviatura, $idNivel, $profesores, $aulas, $precio);
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

    header("Location: /pfc/vistas/admin/ciclos/modificarCiclos.php?idCiclo=" . $id);
    exit;
}

header("Location: /pfc/vistas/admin/ciclos/verCiclos.php");
exit;
?>