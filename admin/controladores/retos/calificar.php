<?php
session_start();
require_once "../../../modelos/retos.php";

if (isset($_POST['guardarNotas'])) {
    $listaCalificaciones = $_POST['notas'];
    $idReto = $_POST['idReto'];

    foreach ($listaCalificaciones as $idEstudiante => $nota) {
        if ($nota !== "") {
            calificarReto($idEstudiante, $idReto, $nota);
        }
    }

    $_SESSION['exito'] = "Calificaciones actualizadas correctamente.";
    header("Location: ../../vistas/retos/verRetos.php");
    exit;
}

header("Location: ../../vistas/retos/verRetos.php");
exit;
?>