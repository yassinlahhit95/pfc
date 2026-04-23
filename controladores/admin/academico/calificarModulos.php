<?php
session_start();
require_once "../../../modelos/calificaciones.php";

if (isset($_POST['guardarNotas'])) {
    $id_modulo = $_POST['idModulo'];
    $ids_estudiantes = $_POST['estudiantes'];
    $notas = $_POST['notas'];
    $observaciones = $_POST['observaciones'];

    $error_al_guardar = false;

    for ($i = 0; $i < count($ids_estudiantes); $i++) {
        $id_est = $ids_estudiantes[$i];
        $nota = $notas[$i];
        $obs = $observaciones[$i];

        if ($nota != "" && ($nota < 0 || $nota > 10)) {
            $error_al_guardar = true;
            continue;
        }

        if (!actualizarOCrearNota($id_est, $id_modulo, $nota, $obs)) {
            $error_al_guardar = true;
        }
    }

    if ($error_al_guardar) {
        $_SESSION['error'] = "Se produjeron algunos errores al guardar las notas. Revise los valores.";
    } else {
        $_SESSION['exito'] = "Todas las calificaciones se guardaron correctamente.";
    }

    header("Location: /pfc/vistas/admin/academico/calificacionesModulos.php?idModulo=$id_modulo");
    exit;
}

header("Location: /pfc/vistas/admin/academico/calificacionesModulos.php");
exit;
