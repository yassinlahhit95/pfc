<?php
session_start();
require_once __DIR__ . "/../../../modelos/aula.php";

if (empty($_SESSION['idProfesor'])) { header("Location: ../../../vistas/login.php"); exit; }

$idTarea   = intval($_GET['id'] ?? 0);
$idModulo  = intval($_GET['modulo'] ?? 0);

if ($idTarea > 0) {
    $tarea = obtenerTareaPorIdAula($idTarea);
    if ($tarea && $tarea['idProfesor'] == $_SESSION['idProfesor']) {
        borrarTareaAula($idTarea);
        $_SESSION['exito'] = "Tarea eliminada.";
        $idModulo = $idModulo ?: $tarea['idModulo'];
    }
}

header("Location: ../../../vistas/profesores/aula/modulo.php?id=$idModulo");
exit;
