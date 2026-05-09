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

    $errores = [];
    if (empty($nombre)) {
        $errores['nombreCiclo'] = "Nombre obligatorio.";
    }
    if (empty($abreviatura)) {
        $errores['abreviaturaCiclo'] = "Abreviatura obligatoria.";
    }
    if (empty($idNivelEducativo)) {
        $errores['idNivel'] = "Nivel obligatorio.";
    }
    if (!is_numeric($precioCiclo) || $precioCiclo < 0) {
        $errores['precioCiclo'] = "El precio debe ser un número válido.";
    }

    if (empty($errores)) {
        if (checkCicloExistente($nombre, $abreviatura)) {
            $errores['nombreCiclo'] = "El nombre o la abreviatura ya existen.";
        }
    }

    if (empty($errores)) {
        $resultado = insertarNuevoCiclo($nombre, $abreviatura, $idNivelEducativo, $profesores, $aulas, $precioCiclo);
        if ($resultado) {
            $_SESSION['exito'] = "Ciclo registrado correctamente.";
            header("Location: ../../../vistas/admin/ciclos/verCiclos.php");
            exit;
        }
        $_SESSION['error'] = "No se pudo registrar el ciclo en la base de datos.";
    } else {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_ciclo'] = $_POST;
    }

    header("Location: ../../../vistas/admin/ciclos/agregarCiclos.php");
    exit;
}

header("Location: ../../../vistas/admin/ciclos/verCiclos.php");
exit;
?>
