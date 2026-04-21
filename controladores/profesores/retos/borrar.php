<?php
session_start();
require_once "../../../modelos/retos.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    eliminarReto($id);
}

header("Location: /pfc/vistas/profesores/retos/lista.php");
exit;
?>