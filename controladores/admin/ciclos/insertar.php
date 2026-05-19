<?php
session_start();
require_once __DIR__ . "/../../../modelos/ciclos.php";

if (isset($_POST['guardarCiclo'])) {
    $nombre = trim($_POST['nombreCiclo']);
    $abreviatura = trim($_POST['abreviaturaCiclo']);
    $idNivelEducativo = $_POST['idNivel'];
    $precioCiclo = trim($_POST['precioCiclo']);
    $profesores = $_POST['profesores'] ?? [];

    $fallos = [];
    if (empty($nombre)) $fallos['nombreCiclo'] = "Nombre obligatorio.";
    if (empty($abreviatura)) $fallos['abreviaturaCiclo'] = "Abreviatura obligatoria.";
    if (empty($idNivelEducativo)) $fallos['idNivel'] = "Nivel obligatorio.";
    if (!is_numeric($precioCiclo) || $precioCiclo < 0) $fallos['precioCiclo'] = "El precio debe ser un número válido.";

    if (empty($fallos) && checkCicloExistente($nombre, $abreviatura)) {
        $fallos['nombreCiclo'] = "El nombre o la abreviatura ya existen.";
    }

    if (!empty($fallos)) {
        $_SESSION['errores'] = $fallos;
        $_SESSION['datos_ciclo'] = $_POST;
        header("Location: ../../../vistas/admin/ciclos/agregarCiclos.php");
        exit;
    }

    if (insertarNuevoCiclo($nombre, $abreviatura, $idNivelEducativo, $profesores, $precioCiclo)) {
        $_SESSION['exito'] = "Ciclo registrado correctamente.";
        header("Location: ../../../vistas/admin/ciclos/verCiclos.php");
        exit;
    }
    $_SESSION['error'] = "No se pudo registrar el ciclo en la base de datos.";
    header("Location: ../../../vistas/admin/ciclos/agregarCiclos.php");
    exit;
}

header("Location: ../../../vistas/admin/ciclos/verCiclos.php");
exit;
?>
