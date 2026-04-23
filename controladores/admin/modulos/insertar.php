<?php
session_start();
require_once "../../../modelos/modulos.php";

if (isset($_POST['guardarModulo'])) {
    $nombre_modulo = $_POST['nombreModulo'];
    $id_del_ciclo = $_POST['idCiclo'];
    $horas_maximas = $_POST['horasMaximas'];

    $lista_de_errores = array();

    if (empty($nombre_modulo)) {
        $lista_de_errores['nombreModulo'] = "El nombre del módulo es obligatorio.";
    }
    
    if (empty($id_del_ciclo)) {
        $lista_de_errores['idCiclo'] = "Debe seleccionar un ciclo formativo.";
    }
    
    if (empty($horas_maximas)) {
        $lista_de_errores['horasMaximas'] = "Las horas máximas son obligatorias.";
    } else {
        if (!is_numeric($horas_maximas)) {
            $lista_de_errores['horasMaximas'] = "Las horas deben ser un valor numérico.";
        }
    }

    if (empty($lista_de_errores)) {
        $resultado = insertarModulo($nombre_modulo, $id_del_ciclo, $horas_maximas);
        if ($resultado) {
            $_SESSION['exito'] = "Módulo registrado correctamente.";
            header("Location: /pfc/vistas/admin/modulos/verModulos.php");
            exit;
        } else {
            $_SESSION['error'] = "Error al insertar el módulo en la base de datos.";
        }
    } else {
        $_SESSION['errores'] = $lista_de_errores;
        $_SESSION['datos_modulo'] = $_POST;
    }

    header("Location: /pfc/vistas/admin/modulos/agregarModulos.php");
    exit;
}

header("Location: /pfc/vistas/admin/modulos/verModulos.php");
exit;
