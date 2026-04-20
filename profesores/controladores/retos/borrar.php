<?php
session_start();
require_once "../../../modelos/retos.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    eliminarReto($id);
}

header("Location: ../../vistas/retos/lista.php");
exit;
?>