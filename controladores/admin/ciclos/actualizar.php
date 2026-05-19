<?php
session_start();
require_once __DIR__ . "/../../../modelos/ciclos.php";

if (isset($_POST['actualizarCiclo'])) {
    $idCiclo = trim($_POST['idCiclo']);
    $nombre = trim($_POST['nombreCiclo']);
    $abreviatura = trim($_POST['abreviaturaCiclo']);
    $idNivelEducativo = trim($_POST['idNivel']);
    $precioCiclo = trim($_POST['precioCiclo']);
    $profesores = $_POST['profesores'] ?? [];

    $fallos = [];
    if (empty($nombre)) $fallos['nombreCiclo'] = "Nombre obligatorio.";
    if (empty($abreviatura)) $fallos['abreviaturaCiclo'] = "Abreviatura obligatoria.";
    if (!is_numeric($precioCiclo) || $precioCiclo < 0) $fallos['precioCiclo'] = "El precio debe ser un número válido.";

    if (empty($fallos) && checkCicloExistente($nombre, $abreviatura, $idCiclo)) {
        $fallos['nombreCiclo'] = "El nombre o la abreviatura ya están en uso.";
    }

    if (!empty($fallos)) {
        $_SESSION['errores'] = $fallos;
        $_SESSION['datos_ciclos'] = $_POST;
        header("Location: ../../../vistas/admin/ciclos/modificarCiclos.php?idCiclo=" . $idCiclo);
        exit;
    }

    if (actualizarCicloExistente($idCiclo, $nombre, $abreviatura, $idNivelEducativo, $profesores, $precioCiclo)) {
        $_SESSION['exito'] = "Ciclo actualizado correctamente.";
        header("Location: ../../../vistas/admin/ciclos/verCiclos.php");
        exit;
    }
    $_SESSION['error'] = "No se realizaron cambios o no se pudo actualizar el ciclo en la base de datos.";
    header("Location: ../../../vistas/admin/ciclos/modificarCiclos.php?idCiclo=" . $idCiclo);
    exit;
}

header("Location: ../../../vistas/admin/ciclos/verCiclos.php");
exit;
?>
