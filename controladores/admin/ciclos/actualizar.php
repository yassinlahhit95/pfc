<?php
session_start();
require_once __DIR__ . "/../../../modelos/ciclos.php";

if (isset($_POST['actualizarCiclo'])) {
    $idCiclo = trim($_POST['idCiclo']);
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

    // Comprobamos duplicados
    if (empty($errores_campos)) {
        if (checkCicloExistente($nombre, $abreviatura, $idCiclo)) {
            $errores_campos['nombreCiclo'] = "El nombre o la abreviatura ya están en uso.";
        }
    }

    if (empty($errores_campos)) {
        $resultado = actualizarCicloExistente($idCiclo, $nombre, $abreviatura, $idNivelEducativo, $profesores, $aulas, $precioCiclo);
        if ($resultado) {
            $_SESSION['exito'] = "Ciclo actualizado.";
            header("Location: ../../../vistas/admin/ciclos/verCiclos.php");
            exit;
        }
        $_SESSION['error'] = "No se pudo actualizar el ciclo.";
    } else {
        $_SESSION['errores'] = $errores_campos;
        $_SESSION['datos_ciclos'] = $_POST;
    }

    header("Location: ../../../vistas/admin/ciclos/modificarCiclos.php?idCiclo=" . $idCiclo);
    exit;
}

header("Location: ../../../vistas/admin/ciclos/verCiclos.php");
exit;


