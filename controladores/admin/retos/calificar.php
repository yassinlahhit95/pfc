<?php
session_start();
require_once __DIR__ . "/../../../modelos/retos.php";

$hayError = false;

if (isset($_POST['guardarNotas'])) {
    $notas = $_POST['notas'] ?? [];
    $idReto = trim($_POST['idReto'] ?? '');
    
    if (!empty($idReto) && !empty($notas)) {
        foreach ($notas as $idEstudiante => $nota) {
            calificarReto(trim($idEstudiante), $idReto, trim($nota));
        }
        $_SESSION['exito'] = "Listo! Las notas han sido guardadas.";
    } else {
        $hayError = true;
        $_SESSION['error'] = "Vaya, parece que faltan datos para calificar.";
    }
    
    header("Location: ../../../vistas/admin/retos/verRetos.php");
    exit;
}

header("Location: ../../../vistas/admin/retos/verRetos.php");
exit;
