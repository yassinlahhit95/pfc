<?php
session_start();
require_once "../../../modelos/ciclos.php";

if (isset($_POST['guardarCiclo'])) {
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
    
    if (empty($idNivel)) {
        $lista_de_errores['idNivel'] = "El nivel es obligatorio.";
    }

    if (empty($lista_de_errores)) {
        // Signature updated: insertarNuevoCiclo($nombre, $abreviatura, $idNivel, $profesores, $aulas, $precio)
        $resultado = insertarNuevoCiclo($nombre, $abreviatura, $idNivel, $profesores, $aulas, $precio);
        if ($resultado) {
            $_SESSION['exito'] = "Ciclo registrado correctamente.";
            header("Location: /pfc/vistas/admin/ciclos/verCiclos.php");
            exit;
        } else {
            $_SESSION['error'] = "Error al insertar en la base de datos.";
        }
    } else {
        $_SESSION['errores'] = $lista_de_errores;
        $_SESSION['datos_ciclo'] = $_POST;
    }

    header("Location: /pfc/vistas/admin/ciclos/agregarCiclos.php");
    exit;
}

header("Location: /pfc/vistas/admin/ciclos/verCiclos.php");
exit;
?>