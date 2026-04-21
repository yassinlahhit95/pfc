<?php
session_start();
require_once "../../../modelos/tfg.php";

if (isset($_GET['id'])) {
    eliminarArchivoTFG($_GET['id']);
}

header("Location: /pfc/vistas/profesores/tfg/lista.php");
exit;
?>