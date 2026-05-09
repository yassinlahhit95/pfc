<?php
session_start();
require_once __DIR__ . "/../../../modelos/retos.php";

if (isset($_POST['guardarReto'])) {
    $nombreReto = trim($_POST['nombreReto']);
    $horasReto = trim($_POST['horasReto']);
    $fechaInicioReto = trim($_POST['fechaInicioReto']);
    $fechaFinReto = trim($_POST['fechaFinReto']);
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
    } else if (!empty($fechaInicioReto) && $fechaFinReto < $fechaInicioReto) {
        $listaDeErrores['fechaFinReto'] = "La fecha de fin no puede ser anterior a la de inicio.";
    }

    if (!empty($fechaInicioReto) && !empty($fechaFinReto) && !empty($horasReto) && is_numeric($horasReto) && $fechaInicioReto <= $fechaFinReto) {
        $begin = new DateTime($fechaInicioReto);
        $end = new DateTime($fechaFinReto);
        $diasLaborables = 0;
        while ($begin <= $end) {
            if ($begin->format('N') < 6) {
                $diasLaborables++;
            }
            $begin->modify('+1 day');
        }
        $maxHorasPermitidas = $diasLaborables * 6;
        if ($horasReto > $maxHorasPermitidas) {
            $listaDeErrores['horasReto'] = "Las horas estimadas superan el máximo de $maxHorasPermitidas h para el periodo seleccionado (6h/día laborable).";
        }
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
            $_SESSION['exito'] = "Reto creado correctamente.";
            header("Location: ../../../vistas/admin/retos/verRetos.php");
            exit;
        }
        $_SESSION['error'] = "No se pudo crear el reto en la base de datos.";
    } else {
        $_SESSION['errores'] = $listaDeErrores;
        $_SESSION['datos_reto'] = $_POST;
    }

    header("Location: ../../../vistas/admin/retos/agregarRetos.php");
    exit;
}

header("Location: ../../../vistas/admin/retos/verRetos.php");
exit;
?>
