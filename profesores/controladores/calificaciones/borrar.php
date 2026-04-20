<?php
session_start();
require_once "../../../modelos/calificaciones.php";

if (isset($_GET['id'])) {
    eliminarCalificacionModulo($_GET['id']);
}

header("Location: ../../vistas/calificaciones/lista.php");
exit;
?>