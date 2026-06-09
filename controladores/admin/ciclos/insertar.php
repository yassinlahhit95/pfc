<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

if (isset($_POST['guardarCiclo'])) {
    $nombre = trim($_POST['nombreCiclo']);
    $abreviatura = trim($_POST['abreviaturaCiclo']);
    $idNivelEducativo = $_POST['idNivel'];
    $precioCiclo = trim($_POST['precioCiclo']);
    $profesores = $_POST['profesores'] ?? [];

    $errores = '';
    if (empty($nombre)) $errores = "Nombre obligatorio.";
    if (empty($abreviatura)) $errores = "Abreviatura obligatoria.";
    if (empty($idNivelEducativo)) $errores = "Nivel obligatorio.";
    if (!is_numeric($precioCiclo) || $precioCiclo < 0) $errores = "El precio debe ser un número válido.";

    if (!$errores && checkCicloExistente($nombre, $abreviatura)) {
        $errores = "El nombre o la abreviatura ya existen.";
    }

    if ($errores) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_ciclo'] = $_POST;
        header("Location: ../../../vistas/admin/ciclos/agregarCiclos.php");
        exit;
    }

    if (insertarNuevoCiclo($nombre, $abreviatura, $idNivelEducativo, $profesores, $precioCiclo)) {
        $_SESSION['exito'] = "Ciclo registrado correctamente.";
        header("Location: ../../../vistas/admin/ciclos/verCiclos.php");
        exit;
    }
    $_SESSION['errores'] = "No se pudo registrar el ciclo en la base de datos.";
    header("Location: ../../../vistas/admin/ciclos/agregarCiclos.php");
    exit;
}

header("Location: ../../../vistas/admin/ciclos/verCiclos.php");
exit;
?>
