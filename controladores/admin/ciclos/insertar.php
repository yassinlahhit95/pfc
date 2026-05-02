<?php
session_start();
require_once __DIR__ . "/../../../modelos/ciclos.php";

if (isset($_POST['guardarCiclo'])) {
    $nombre = trim($_POST['nombreCiclo']);
    $abreviatura = trim($_POST['abreviaturaCiclo']);
    $idNivelEducativo = trim($_POST['idNivel']);
    $precioCiclo = trim($_POST['precioCiclo']);
    
    $profesores = isset($_POST['profesores']) ? $_POST['profesores'] : [];
    $aulas = isset($_POST['aulas']) ? $_POST['aulas'] : [];

    $hayError = false;

    if (empty($nombre)) {
        $hayError = true;
        $_SESSION['error'] = "Nombre obligatorio.";
    } elseif (empty($abreviatura)) {
        $hayError = true;
        $_SESSION['error'] = "Abreviatura obligatoria.";
    } elseif (empty($idNivelEducativo)) {
        $hayError = true;
        $_SESSION['error'] = "Nivel obligatorio.";
    }

    if (!$hayError) {
        $resultado = insertarNuevoCiclo($nombre, $abreviatura, $idNivelEducativo, $profesores, $aulas, $precioCiclo);
        if ($resultado) {
            $_SESSION['exito'] = "Ciclo registrado.";
            header("Location: ../../../vistas/admin/ciclos/verCiclos.php");
            exit;
        } else {
            $_SESSION['error'] = "Error al guardar.";
        }
    } else {
        $_SESSION['datos_ciclo'] = $_POST;
    }

    header("Location: ../../../vistas/admin/ciclos/agregarCiclos.php");
    exit;
}

header("Location: ../../../vistas/admin/ciclos/verCiclos.php");
exit;
?>
