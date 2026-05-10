<?php
session_start();
require_once __DIR__ . "/../../../modelos/pagos.php";

if (isset($_GET['idEstudiante'])) {
    $idEstudiante = trim($_GET['idEstudiante']);
    $estado = obtenerEstadoFinancieroEstudiante($idEstudiante);
    echo $estado['totalPagado'] . "," . $estado['precioCiclo'] . "," . $estado['restante'];
}
?>
