<?php
session_start();
require_once __DIR__ . "/../../../modelos/retos.php";

$hayError = false;

if (isset($_POST['guardarReto'])) {
    $nombreReto = trim($_POST['nombreReto'] ?? '');
    $horasReto = trim($_POST['horasReto'] ?? '');
    $fechaInicioReto = trim($_POST['fechaInicioReto'] ?? '');
    $fechaFinReto = trim($_POST['fechaFinReto'] ?? '');
    $listaModulos = $_POST['modulosReto'] ?? [];

    $listaDeErrores = [];

    if (empty($nombreReto)) {
        $listaDeErrores['nombreReto'] = "El nombre es obligatorio.";
    }
    
    if (empty($horasReto)) {
        $listaDeErrores['horasReto'] = "Las horas son obligatorias.";
    } else if (!is_numeric($horasReto)) {
        $listaDeErrores['horasReto'] = "Las horas deben ser un número.";
    }
    
    $hoy = date('Y-m-d');
    if (empty($fechaInicioReto)) {
        $listaDeErrores['fechaInicioReto'] = "La fecha de inicio es obligatoria.";
    } else if ($fechaInicioReto < $hoy) {
        $listaDeErrores['fechaInicioReto'] = "La fecha no puede ser anterior a hoy.";
    }

    if (empty($fechaFinReto)) {
        $listaDeErrores['fechaFinReto'] = "La fecha de fin es obligatoria.";
    } else if ($fechaFinReto < $fechaInicioReto) {
        $listaDeErrores['fechaFinReto'] = "La fecha de fin no puede ser anterior a la de inicio.";
    }

    if (empty($listaModulos)) {
        $listaDeErrores['modulosReto'] = "Debes seleccionar al menos un módulo.";
    } else if (is_numeric($horasReto)) {
        foreach ($listaModulos as $idModulo) {
            if (!comprobarHorasDisponiblesModulo($idModulo, $horasReto, null)) {
                $listaDeErrores['modulosReto'] = "Un módulo no tiene suficientes horas.";
                break;
            }
        }
    }

    if (empty($listaDeErrores)) {
        $resultado = insertarReto($nombreReto, $fechaInicioReto, $fechaFinReto, $horasReto, $listaModulos);
        if ($resultado) {
            $_SESSION['exito'] = "Reto creado.";
            header("Location: ../../../vistas/admin/retos/verRetos.php");
            exit;
        } else {
            $hayError = true;
            $_SESSION['error'] = "No se pudo crear el reto.";
        }
    } else {
        $hayError = true;
        $_SESSION['errores'] = $listaDeErrores;
        $_SESSION['datos_reto'] = $_POST;
    }

    header("Location: ../../../vistas/admin/retos/agregarRetos.php");
    exit;
}

header("Location: ../../../vistas/admin/retos/verRetos.php");
exit;
