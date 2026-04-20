<?php
session_start();
require_once "../../../modelos/reclamaciones.php";

if (isset($_GET['id'])) {
    eliminarReclamacion($_GET['id']);
}

header("Location: ../../vistas/reclamaciones/lista.php");
exit;
?>