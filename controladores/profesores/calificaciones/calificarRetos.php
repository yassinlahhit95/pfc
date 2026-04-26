<?php
session_start();
require_once "../../../modelos/retos.php";

if (isset($_POST['guardarNotasReto'])) {
    $id_reto = $_POST['idReto'];
    $id_ciclo = $_POST['idCiclo'];
    $id_modulo = $_POST['idModulo'];
    
    $ids_estudiantes = $_POST['estudiantes'];
    $notas = $_POST['notas'];

    $error_al_guardar = false;

    for ($i = 0; $i < count($ids_estudiantes); $i++) {
        $id_est = $ids_estudiantes[$i];
        $nota = $notas[$i];

        // Validar que sea numérico o vacío
        if ($nota != "") {
            if (!is_numeric($nota)) {
                $error_al_guardar = true;
            } else {
                if ($nota < 0 || $nota > 10) {
                    $error_al_guardar = true;
                }
            }
        }

        if ($error_al_guardar == false) {
            $nota_final = $nota;
            if ($nota_final == "") { $nota_final = 0; }
            
            if (!calificarReto($id_est, $id_reto, $nota_final)) {
                $error_al_guardar = true;
            }
        }
    }

    if ($error_al_guardar == true) {
        $_SESSION['error'] = "Hubo errores al procesar algunas notas. Asegúrese de que sean números entre 0 y 10.";
    } else {
        $_SESSION['exito'] = "Calificaciones del reto guardadas con éxito.";
    }

    header("Location: /pfc/vistas/profesores/calificaciones/retos.php?idCiclo=" . $id_ciclo . "&idModulo=" . $id_modulo . "&idReto=" . $id_reto);
    exit;
}

header("Location: /pfc/vistas/profesores/calificaciones/retos.php");
exit;

