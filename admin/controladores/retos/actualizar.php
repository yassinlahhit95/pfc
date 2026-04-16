<?php
session_start();
require_once "../../modelos/retos.php";
require_once "../../modelos/modulos.php";

if (isset($_POST['guardarReto'])) {
    
    unset($_SESSION['errores']);

    $id = $_POST['idReto'] ?? '';
    $nombre = trim($_POST['nombreReto'] ?? '');
    $fechaInicio = $_POST['fechaInicio'] ?? '';
    $fechaFin = $_POST['fechaFin'] ?? '';
    $horas = trim($_POST['horasReto'] ?? '');
    $modulosSeleccionados = $_POST['modulos'] ?? [];
    $errores = [];

    if (empty($id) || !is_numeric($id)) {
        $errores['general'] = "ID del reto no válido";
    }

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
        header("Location: ../../vistas/retos/modificarRetos.php?id=" . $id);
        exit;
    }

    // Business Logic: Check hour constraints
    $modeloReto = new reto();
    $modeloModulo = new modulo();
    $horasInt = intval($horas);
    
    $retoActual = $modeloReto->obtenerRetoPorIdModelo($id);
    $modulosDeRetoActual = $modeloReto->obtenerModulosDeReto($id);
    $idsModulosActuales = array_column($modulosDeRetoActual, 'idModulo');

    foreach ($modulosSeleccionados as $idModulo) {
        $moduloInfo = $modeloModulo->obtenerModuloPorIdModelo($idModulo);
        $horasActuales = $modeloModulo->obtenerHorasTotalesRetosModulo($idModulo);
        
        if (in_array($idModulo, $idsModulosActuales)) {
            $horasActuales -= $retoActual['horasReto'];
        }

        if (($horasActuales + $horasInt) > $moduloInfo['horasMaximas']) {
            $errores['horasReto'] = "El módulo " . $moduloInfo['nombreModulo'] . " superaría las " . $moduloInfo['horasMaximas'] . " horas (Actual: $horasActuales, Nuevo: $horasInt)";
            $_SESSION['errores'] = $errores;
            header("Location: ../../vistas/retos/modificarRetos.php?id=" . $id);
            exit;
        }
    }

    if ($modeloReto->actualizarRetoModelo($id, $nombre, $fechaInicio, $fechaFin, $horasInt)) {
        $modeloReto->limpiarAsociacionesReto($id);
        foreach ($modulosSeleccionados as $idMod) {
            $modeloReto->asociarModuloReto($idMod, $id);
        }
        $_SESSION['exito'] = "Reto actualizado correctamente";
    } else {
        $_SESSION['error'] = "Error al actualizar el reto";
    }

    header("Location: ../../vistas/retos/verRetos.php");
    exit;
}
?>
