<?php
session_start();
require_once "../../modelos/ciclos.php";

if (isset($_POST['guardarCiclo'])) {
    
    unset($_SESSION['errores']);
    unset($_SESSION['datos_ciclo']);

    $nombre = trim($_POST['nombreCiclo'] ?? '');
    $idNivel = $_POST['idNivel'] ?? '';
    $idEstado = $_POST['idEstado'] ?? '';
    $descripcion = trim($_POST['descripcionCiclo'] ?? '');
    $profesores = $_POST['profesores'] ?? [];
    $aulas = $_POST['aulas'] ?? [];
    
    $errores = [];

    if (empty($nombre)) $errores['nombreCiclo'] = "El nombre del ciclo es obligatorio";
    
    if (empty($idNivel)) {
        $errores['idNivel'] = "El nivel educativo es obligatorio";
    } elseif (!is_numeric($idNivel) || !preg_match('/^[0-9]+$/', $idNivel) || !ctype_digit($idNivel)) {
        $errores['idNivel'] = "El nivel educativo debe ser un número entero válido";
    }

    if (empty($idEstado)) {
        $errores['idEstado'] = "El estado es obligatorio";
    } elseif (!is_numeric($idEstado) || !preg_match('/^[0-9]+$/', $idEstado) || !ctype_digit($idEstado)) {
        $errores['idEstado'] = "El estado debe ser un número entero válido";
    }

    if (empty($descripcion)) $errores['descripcionCiclo'] = "La descripción es obligatoria";
    
    if (empty($profesores)) {
        $errores['profesores'] = "Debes seleccionar al menos un profesor";
    }
    
    if (empty($aulas)) {
        $errores['aulas'] = "Debes seleccionar al menos un aula";
    }

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_ciclo'] = $_POST;
        header("Location: ../../vistas/ciclos/agregarCiclos.php");
        exit;
    }

    $modelo = new ciclo();
    if ($modelo->insertarCicloModelo($nombre, $descripcion, $idNivel, $idEstado, $profesores, $aulas)) {
        $_SESSION['exito'] = "Ciclo creado correctamente";
    } else {
        $_SESSION['error'] = "Error al crear el ciclo";
    }

    header("Location: ../../vistas/ciclos/verCiclos.php");
    exit;
}
?>
