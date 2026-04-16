<?php
session_start();
require_once "../../modelos/retos.php";

if (isset($_POST['idReto'])) {
    $idReto = $_POST['idReto'];
    $notas = $_POST['notas'] ?? []; // Array [idEstudiante => nota]
    $modeloReto = new reto();
    
    $errores_notas = [];
    foreach ($notas as $idEstudiante => $nota) {
        $nota = trim($nota);
        if ($nota !== "") {
            // Check if numeric and matches float/int pattern
            if (!is_numeric($nota) || !preg_match('/^[0-9]+(\.[0-9]+)?$/', $nota)) {
                $errores_notas[] = "La nota para el estudiante $idEstudiante no es válida";
                continue;
            }
            
            $nota_val = floatval($nota);
            if ($nota_val < 0 || $nota_val > 10) {
                $errores_notas[] = "La nota para el estudiante $idEstudiante debe estar entre 0 y 10";
                continue;
            }

            $modeloReto->calificarRetoEstudiante($idEstudiante, $idReto, $nota_val);
        }
    }
    
    if (!empty($errores_notas)) {
        $_SESSION['errores_notas'] = $errores_notas;
    }
    $_SESSION['exito'] = "Calificaciones procesadas correctamente";
    header("Location: ../../vistas/retos/calificarReto.php?id=" . $idReto);
    exit;
}

header("Location: ../../vistas/retos/verRetos.php");
exit;
?>
