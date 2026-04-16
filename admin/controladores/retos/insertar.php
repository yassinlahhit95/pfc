<?php
session_start();
require_once "../../modelos/retos.php";
require_once "../../modelos/modulos.php";

if (isset($_POST['guardarReto'])) {
    
    unset($_SESSION['errores']);
    unset($_SESSION['datos_reto']);

    $nombre = trim($_POST['nombreReto'] ?? '');
    $fechaInicio = $_POST['fechaInicio'] ?? '';
    $fechaFin = $_POST['fechaFin'] ?? '';
    $horas = trim($_POST['horasReto'] ?? '');
    $modulosSeleccionados = $_POST['modulos'] ?? [];
    $errores = [];

    if (empty($nombre)) {
        $errores['nombreReto'] = "El nombre del reto es obligatorio";
    }

    if (empty($fechaInicio)) {
        $errores['fechaInicio'] = "La fecha de inicio es obligatoria";
    }

    if (empty($fechaFin)) {
        $errores['fechaFin'] = "La fecha de fin es obligatoria";
    }

    if (empty($horas)) {
        $errores['horasReto'] = "El número de horas es obligatorio";
    } elseif (!is_numeric($horas)) {
        $errores['horasReto'] = "Las horas deben ser un valor numérico";
    } elseif (!preg_match('/^[0-9]+$/', $horas)) {
        $errores['horasReto'] = "Las horas deben ser un número entero positivo (formato no válido)";
    } elseif (!ctype_digit($horas)) {
        $errores['horasReto'] = "Las horas deben contener solo dígitos";
    } elseif (intval($horas) <= 0) {
        $errores['horasReto'] = "Las horas deben ser mayores a cero";
    }

    if (empty($modulosSeleccionados)) {
        $errores['modulos'] = "Debes seleccionar al menos un módulo";
    }

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_reto'] = $_POST;
        header("Location: ../../vistas/retos/agregarRetos.php");
        exit;
    }

    // Business Logic: Check hour constraints
    $modeloModulo = new modulo();
    $horasInt = intval($horas);
    foreach ($modulosSeleccionados as $idModulo) {
        $moduloInfo = $modeloModulo->obtenerModuloPorIdModelo($idModulo);
        $horasActuales = $modeloModulo->obtenerHorasTotalesRetosModulo($idModulo);
        
        if (($horasActuales + $horasInt) > $moduloInfo['horasMaximas']) {
            $errores['horasReto'] = "El módulo " . $moduloInfo['nombreModulo'] . " superaría las " . $moduloInfo['horasMaximas'] . " horas (Actual: $horasActuales, Nuevo: $horasInt)";
            $_SESSION['errores'] = $errores;
            $_SESSION['datos_reto'] = $_POST;
            header("Location: ../../vistas/retos/agregarRetos.php");
            exit;
        }
    }

    $modeloReto = new reto();
    $idReto = $modeloReto->insertarRetoModelo($nombre, $fechaInicio, $fechaFin, $horasInt);
    if ($idReto) {
        foreach ($modulosSeleccionados as $idModulo) {
            $modeloReto->asociarModuloReto($idModulo, $idReto);
        }
        $_SESSION['exito'] = "Reto creado correctamente";
    } else {
        $_SESSION['error'] = "Error al crear el reto";
    }

    header("Location: ../../vistas/retos/verRetos.php");
    exit;
}
?>
