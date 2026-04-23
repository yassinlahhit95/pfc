<?php
session_start();
require_once "../../../modelos/pagos.php";

if (isset($_GET['idEstudiante'])) {
    $idEstudiante = $_GET['idEstudiante'];
    $estado = obtenerEstadoFinancieroEstudiante($idEstudiante);
    echo json_encode($estado);
}
?>