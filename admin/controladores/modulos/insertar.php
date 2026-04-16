<?php
session_start();
require_once "../../modelos/modulos.php";

if (isset($_POST['guardarModulo'])) {
    
    unset($_SESSION['errores']);
    unset($_SESSION['datos_modulo']);

    $nombre = trim($_POST['nombreModulo'] ?? '');
    $idCiclo = $_POST['idCiclo'] ?? '';
    $horasMaximas = $_POST['horasMaximas'] ?? '';
    $errores = [];

    if (empty($nombre)) {
        $errores['nombreModulo'] = "El nombre del módulo es obligatorio";
    }

    if (empty($idCiclo)) {
        $errores['idCiclo'] = "Debes seleccionar un ciclo";
    } elseif (!is_numeric($idCiclo) || !preg_match('/^[0-9]+$/', $idCiclo) || !ctype_digit($idCiclo)) {
        $errores['idCiclo'] = "El ciclo debe ser un número entero válido";
    }

    if (empty($horasMaximas)) {
        $errores['horasMaximas'] = "Las horas máximas deben ser un número positivo";
    } elseif (!is_numeric($horasMaximas) || !preg_match('/^[0-9]+$/', $horasMaximas) || !ctype_digit($horasMaximas)) {
        $errores['horasMaximas'] = "Las horas máximas deben ser un número entero válido";
    } elseif ($horasMaximas <= 0) {
        $errores['horasMaximas'] = "Las horas máximas deben ser un número positivo";
    }

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_modulo'] = $_POST;
        header("Location: ../../vistas/modulos/agregarModulos.php");
        exit;
    }

    $modelo = new modulo();
    if ($modelo->insertarModuloModelo($nombre, $idCiclo, $horasMaximas)) {
        $_SESSION['exito'] = "Módulo creado correctamente";
    } else {
        $_SESSION['error'] = "Error al crear el módulo";
    }

    header("Location: ../../vistas/modulos/verModulos.php");
    exit;
}
?>
