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
        $listaDeErrores['nombreReto'] = "Vaya, el nombre es obligatorio.";
    }
    
    if (empty($horasReto)) {
        $listaDeErrores['horasReto'] = "Vaya, las horas son obligatorias.";
    } else if (!is_numeric($horasReto)) {
        $listaDeErrores['horasReto'] = "Vaya, las horas deben ser un nÃºmero.";
    }
    
    $hoy = date('Y-m-d');
    if (empty($fechaInicioReto)) {
        $listaDeErrores['fechaInicioReto'] = "Vaya, la fecha de inicio es obligatoria.";
    } else if ($fechaInicioReto < $hoy) {
        $listaDeErrores['fechaInicioReto'] = "Vaya, la fecha de inicio no puede ser anterior a hoy.";
    }

    if (empty($fechaFinReto)) {
        $listaDeErrores['fechaFinReto'] = "Vaya, la fecha de fin es obligatoria.";
    } else if ($fechaFinReto < $fechaInicioReto) {
        $listaDeErrores['fechaFinReto'] = "Vaya, la fecha de fin no puede ser anterior a la de inicio.";
    }

    if (empty($listaModulos)) {
        $listaDeErrores['modulosReto'] = "Vaya, debes seleccionar al menos un mÃ³dulo.";
    } else if (is_numeric($horasReto)) {
        foreach ($listaModulos as $idModulo) {
            if (!comprobarHorasDisponiblesModulo($idModulo, $horasReto, null)) {
                $listaDeErrores['modulosReto'] = "Vaya, uno de los mÃ³dulos no tiene suficientes horas.";
                break;
            }
        }
    }

    if (empty($listaDeErrores)) {
        $resultado = insertarReto($nombreReto, $fechaInicioReto, $fechaFinReto, $horasReto, $listaModulos);
        if ($resultado) {
            $_SESSION['exito'] = "Listo! El reto ha sido creado.";
            header("Location: ../../../vistas/admin/retos/verRetos.php");
            exit;
        } else {
            $hayError = true;
            $_SESSION['error'] = "Vaya, no se pudo guardar el reto en la base de datos.";
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
