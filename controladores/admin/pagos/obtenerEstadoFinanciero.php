<?php
session_start();
require_once __DIR__ . "/../../../modelos/pagos.php";

if (isset($_GET['idEstudiante'])) {
    $idEstudiante = $_GET['idEstudiante'];
    $estado = obtenerEstadoFinancieroEstudiante($idEstudiante);
    
    // Se evita el uso de JSON por petición del usuario
    echo $estado['totalPagado'] . "," . $estado['precioCiclo'] . "," . $estado['restante'];
}
?>
