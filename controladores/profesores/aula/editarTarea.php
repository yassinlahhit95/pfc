<?php
session_start();
require_once __DIR__ . "/../../../modelos/aula.php";

if (empty($_SESSION['idProfesor'])) { header("Location: ../../../vistas/login.php"); exit; }

if (!isset($_POST['titulo'])) { header("Location: ../../../vistas/profesores/aula/index.php"); exit; }

$idTarea     = intval($_POST['idTarea'] ?? 0);
$titulo      = trim($_POST['titulo'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$idModulo    = intval($_POST['idModulo'] ?? 0);

if ($idTarea > 0 && !empty($titulo)) {
    $tarea = obtenerTareaPorIdAula($idTarea);
    if ($tarea && $tarea['idProfesor'] == $_SESSION['idProfesor']) {
        editarTareaAula($idTarea, $titulo, $descripcion);
        $_SESSION['exito'] = "Tarea actualizada.";
    }
}

header("Location: ../../../vistas/profesores/aula/modulo.php?id=$idModulo");
exit;
