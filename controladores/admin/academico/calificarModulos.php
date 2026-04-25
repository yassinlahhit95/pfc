<?php
session_start();
require_once "../../../modelos/calificaciones.php";

if (isset($_POST['guardarNotas'])) {
    $id_modulo = $_POST['idModulo'];
    $ids_estudiantes = $_POST['estudiantes'];
    $notas_1ev = $_POST['notas_1ev'];
    $notas_1final = $_POST['notas_1final'];
    $notas_2ev = $_POST['notas_2ev'];
    $notas_2final = $_POST['notas_2final'];
    $observaciones = $_POST['observaciones'];

    $error_al_guardar = false;

    for ($i = 0; $i < count($ids_estudiantes); $i++) {
        $id_est = $ids_estudiantes[$i];
        $n1ev = $notas_1ev[$i];
        $n1f = $notas_1final[$i];
        $n2ev = $notas_2ev[$i];
        $n2f = $notas_2final[$i];
        $obs = $observaciones[$i];

        // Validar que sean numéricos o vacíos
        $notas_a_validar = array($n1ev, $n1f, $n2ev, $n2f);
        foreach ($notas_a_validar as $nota) {
            if ($nota != "") {
                if (!is_numeric($nota)) {
                    $error_al_guardar = true;
                } else {
                    if ($nota < 0 || $nota > 10) {
                        $error_al_guardar = true;
                    }
                }
            }
        }

        if ($error_al_guardar == false) {
            if (!actualizarOCrearNotaCompleta($id_est, $id_modulo, $n1ev, $n1f, $n2ev, $n2f, $obs)) {
                $error_al_guardar = true;
            }
        }
    }

    if ($error_al_guardar == false) {
        require_once "../../comunes/notificaciones_grades.php";
        for ($i = 0; $i < count($ids_estudiantes); $i++) {
            $id_est = $ids_estudiantes[$i];
            if (isset($_POST['notificarEstudiantes'])) {
                enviarEmailNotasEstudiante($id_est);
            }
        }
        $_SESSION['exito'] = "Calificaciones guardadas con éxito.";
    } else {
        $_SESSION['error'] = "Hubo errores al procesar algunas notas. Asegúrese de que sean números entre 0 y 10.";
    }

    header("Location: /pfc/vistas/admin/academico/calificacionesModulos.php?idModulo=" . $id_modulo);
    exit;
}

header("Location: /pfc/vistas/admin/academico/calificacionesModulos.php");
exit;
