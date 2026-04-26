<?php
session_start();
require_once "../../../modelos/retos.php";
if (isset($_POST['guardarNotas'])) {
    $notas = $_POST['notas'];
    $idReto = $_POST['idReto'];
    foreach ($notas as $idEst => $nota) {
        calificarReto($idEst, $idReto, $nota);
    }
    $_SESSION['exito'] = "Ok";
    header("Location: /pfc/vistas/admin/retos/verRetos.php");
    exit;
}
header("Location: /pfc/vistas/admin/retos/verRetos.php");
exit;


