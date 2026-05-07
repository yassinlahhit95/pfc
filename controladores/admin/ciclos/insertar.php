<?php
session_start();
require_once __DIR__ . "/../../../modelos/ciclos.php";

if (isset($_POST['guardarCiclo'])) {
    $nombre = trim($_POST['nombreCiclo']);
    $abreviatura = trim($_POST['abreviaturaCiclo']);
    $idNivelEducativo = trim($_POST['idNivel']);
    $precioCiclo = trim($_POST['precioCiclo']);
    
    $profesores = $_POST['profesores'] ?? [];
    $aulas = $_POST['aulas'] ?? [];

    $errores_campos = [];
    if (empty($nombre)) {
        $errores_campos['nombreCiclo'] = "Nombre obligatorio.";
    }
    if (empty($abreviatura)) {
        $errores_campos['abreviaturaCiclo'] = "Abreviatura obligatoria.";
    }
    if (empty($idNivelEducativo)) {
        $errores_campos['idNivel'] = "Nivel obligatorio.";
    }

    if (empty($errores_campos)) {
        if (checkCicloExistente($nombre, $abreviatura)) {
            $errores_campos['nombreCiclo'] = "El nombre o la abreviatura ya existen.";
        }
    }

    if (empty($errores_campos)) {
        $resultado = insertarNuevoCiclo($nombre, $abreviatura, $idNivelEducativo, $profesores, $aulas, $precioCiclo);
        if ($resultado) {
            $_SESSION['exito'] = "Ciclo registrado.";
            header("Location: ../../../vistas/admin/ciclos/verCiclos.php");
            exit;
        }
        $_SESSION['error'] = "No se pudo registrar el ciclo.";
    } else {
        $_SESSION['errores'] = $errores_campos;
        $_SESSION['datos_ciclo'] = $_POST;
    }

    header("Location: ../../../vistas/admin/ciclos/agregarCiclos.php");
    exit;
}

header("Location: ../../../vistas/admin/ciclos/verCiclos.php");
exit;
?>
