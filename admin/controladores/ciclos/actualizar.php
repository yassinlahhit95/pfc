<?php
session_start();
require_once "../../modelos/ciclos.php";

if (isset($_POST['guardarCiclo'])) {
    
    unset($_SESSION['errores']);

    $id = $_POST['idCiclo'] ?? '';
    $nombre = trim($_POST['nombreCiclo'] ?? '');
    $idNivel = $_POST['idNivel'] ?? '';
    $idEstado = $_POST['idEstado'] ?? '';
    $descripcion = trim($_POST['descripcionCiclo'] ?? '');
    $profesores = $_POST['profesores'] ?? [];
    $aulas = $_POST['aulas'] ?? [];
    
    $errores = [];

    if (empty($id)) {
        $errores['idCiclo'] = "ID del ciclo no válido";
    } elseif (!is_numeric($id) || !preg_match('/^[0-9]+$/', $id) || !ctype_digit($id)) {
        $errores['idCiclo'] = "ID del ciclo debe ser un número entero válido";
    }

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
        header("Location: ../../vistas/ciclos/modificarCiclos.php?id=" . $id);
        exit;
    }

    $modelo = new ciclo();
    if ($modelo->actualizarCicloModelo($id, $nombre, $descripcion, $idNivel, $idEstado, $profesores, $aulas)) {
        $_SESSION['exito'] = "Ciclo actualizado correctamente";
    } else {
        $_SESSION['error'] = "Error al actualizar el ciclo";
    }

    header("Location: ../../vistas/ciclos/verCiclos.php");
    exit;
}
?>
