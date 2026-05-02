<?php
session_start();
require_once __DIR__ . "/../../../modelos/ciclos.php";

if (isset($_POST['actualizarCiclo'])) {
    $idCiclo = trim($_POST['idCiclo']);
    $nombre = trim($_POST['nombreCiclo']);
    $abreviatura = trim($_POST['abreviaturaCiclo']);
    $idNivelEducativo = trim($_POST['idNivel']);
    $precioCiclo = trim($_POST['precioCiclo']);

    $profesores = isset($_POST['profesores']) ? $_POST['profesores'] : [];
    $aulas = isset($_POST['aulas']) ? $_POST['aulas'] : [];

    $hayError = false;

    if (empty($nombre)) {
        $hayError = true;
        $_SESSION['error'] = "Nombre obligatorio.";
    } elseif (empty($abreviatura)) {
        $hayError = true;
        $_SESSION['error'] = "Abreviatura obligatoria.";
    }

    if (!$hayError) {
        $resultado = actualizarCicloExistente($idCiclo, $nombre, $abreviatura, $idNivelEducativo, $profesores, $aulas, $precioCiclo);
        if ($resultado) {
            $_SESSION['exito'] = "Ciclo actualizado.";
            header("Location: ../../../vistas/admin/ciclos/verCiclos.php");
            exit;
        } else {
            $_SESSION['error'] = "Error al actualizar.";
        }
    } else {
        $_SESSION['datos_ciclos'] = $_POST;
    }

    header("Location: ../../../vistas/admin/ciclos/modificarCiclos.php?idCiclo=" . $idCiclo);
    exit;
}

header("Location: ../../../vistas/admin/ciclos/verCiclos.php");
exit;
?>
