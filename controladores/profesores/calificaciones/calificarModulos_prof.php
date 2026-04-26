<?php
session_start();
require_once "../../../modelos/calificaciones.php";

if (isset($_POST['guardarNotas'])) {
    $idModuloRecibido = $_POST['idModulo'];
    $idCicloRecibido = $_POST['idCiclo']; 
    $listaEstudiantes = $_POST['estudiantes'];
    $notasEv1 = $_POST['notas_1ev'];
    $notasFinal1 = $_POST['notas_1final'];
    $notasEv2 = $_POST['notas_2ev'];
    $notasFinal2 = $_POST['notas_2final'];
    $listaObservaciones = $_POST['observaciones'];

    $errorDetectado = false;
    $totalEstudiantesAProcesar = count($listaEstudiantes);

    for ($posicion = 0; $posicion < $totalEstudiantesAProcesar; $posicion = $posicion + 1) {
        $idEstudianteActual = $listaEstudiantes[$posicion];
        $notaEv1Actual = $notasEv1[$posicion];
        $notaFinal1Actual = $notasFinal1[$posicion];
        $notaEv2Actual = $notasEv2[$posicion];
        $notaFinal2Actual = $notasFinal2[$posicion];
        $observacionActual = $listaObservaciones[$posicion];

        // Validamos que si el campo NO está vacío, entonces DEBE ser un número
        if (!empty($notaEv1Actual) && !is_numeric($notaEv1Actual)) { $errorDetectado = true; }
        if (!empty($notaFinal1Actual) && !is_numeric($notaFinal1Actual)) { $errorDetectado = true; }
        if (!empty($notaEv2Actual) && !is_numeric($notaEv2Actual)) { $errorDetectado = true; }
        if (!empty($notaFinal2Actual) && !is_numeric($notaFinal2Actual)) { $errorDetectado = true; }

        if ($errorDetectado == false) {
            // Validamos que los números estén entre 0 y 10
            if (is_numeric($notaEv1Actual)) { if ($notaEv1Actual < 0 || $notaEv1Actual > 10) { $errorDetectado = true; } }
            if (is_numeric($notaFinal1Actual)) { if ($notaFinal1Actual < 0 || $notaFinal1Actual > 10) { $errorDetectado = true; } }
            if (is_numeric($notaEv2Actual)) { if ($notaEv2Actual < 0 || $notaEv2Actual > 10) { $errorDetectado = true; } }
            if (is_numeric($notaFinal2Actual)) { if ($notaFinal2Actual < 0 || $notaFinal2Actual > 10) { $errorDetectado = true; } }
        }

        // Si después de todas las validaciones no hay error, enviamos al modelo
        if ($errorDetectado == false) {
            $resultadoBaseDatos = actualizarOCrearNotaCompleta($idEstudianteActual, $idModuloRecibido, $notaEv1Actual, $notaFinal1Actual, $notaEv2Actual, $notaFinal2Actual, $observacionActual);
            if ($resultadoBaseDatos == false) {
                $errorDetectado = true;
            }
        }
    }

    if ($errorDetectado == false) {
        require_once "../../comunes/notificaciones_grades.php";
        if (isset($_POST['notificarEstudiantes']) && !empty($_POST['notificarEstudiantes'])) {
            for ($indiceNotif = 0; $indiceNotif < $totalEstudiantesAProcesar; $indiceNotif = $indiceNotif + 1) {
                $idEstudianteANotificar = $listaEstudiantes[$indiceNotif];
                enviarEmailNotasEstudiante($idEstudianteANotificar);
            }
        }
        $_SESSION['exito'] = strtoupper("CALIFICACIONES GUARDADAS CORRECTAMENTE.");
    } else {
        $_SESSION['error'] = strtoupper("HUBO ERRORES EN LAS NOTAS. DEBEN SER NÚMEROS ENTRE 0 Y 10.");
    }

    $urlRedireccion = "/pfc/vistas/profesores/calificaciones/agregar.php?idCiclo=$idCicloRecibido&idModulo=$idModuloRecibido";
    header("Location: " . $urlRedireccion);
    exit;
}

header("Location: /pfc/vistas/profesores/calificaciones/agregar.php");
exit;
