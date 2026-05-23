<?php
session_start();
require_once __DIR__ . "/../../../modelos/retos.php";

$hayError = false;

if (isset($_POST['guardarNotasReto'])) {
    $idReto = trim($_POST['idReto']);
    $idCiclo = trim($_POST['idCiclo']);
    $idModulo = trim($_POST['idModulo']);
    
    $idsEstudiantes = $_POST['estudiantes'] ?? [];
    $notas = $_POST['notas'] ?? [];

    foreach ($idsEstudiantes as $indice => $idEstudiante) {
        $idEstudiante = trim($idEstudiante);
        $nota = trim($notas[$indice]);

        if (!empty($nota)) {
            if (!is_numeric($nota) || $nota < 0 || $nota > 10) {
                $hayError = true;
            }
        }

        if (!$hayError) {
            if (empty($nota)) {
                eliminarCalificacionReto($idEstudiante, $idReto);
            } else {
                if (!calificarReto($idEstudiante, $idReto, $nota)) {
                    $hayError = true;
                }
            }
        }

        if ($hayError) break;
    }

    if ($hayError) {
        $_SESSION['errores'] = "Las notas deben ser números entre 0 y 10.";
    } else {
        $_SESSION['exito'] = "Calificaciones guardadas.";
    }

    header("Location: ../../../vistas/profesores/academico/calificacionesRetos.php?idCiclo=$idCiclo&idReto=$idReto");
    exit;
}

header("Location: ../../../vistas/profesores/academico/calificacionesRetos.php");
exit;
?>
