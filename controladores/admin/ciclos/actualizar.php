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
    $aulas = $_POST['aulas'] ?? [];

    $errores = [];
    if (empty($nombre)) {
        $errores['nombreCiclo'] = "Nombre obligatorio.";
    }
    if (empty($abreviatura)) {
        $errores['abreviaturaCiclo'] = "Abreviatura obligatoria.";
    }
    if (!is_numeric($precioCiclo) || $precioCiclo < 0) {
        $errores['precioCiclo'] = "El precio debe ser un número válido.";
    }

    if (empty($errores)) {
        if (checkCicloExistente($nombre, $abreviatura, $idCiclo)) {
            $errores['nombreCiclo'] = "El nombre o la abreviatura ya están en uso.";
        }
    }

    if (empty($errores)) {
        $resultado = actualizarCicloExistente($idCiclo, $nombre, $abreviatura, $idNivelEducativo, $profesores, $aulas, $precioCiclo);
        if ($resultado) {
            $_SESSION['exito'] = "Ciclo actualizado.";
            header("Location: ../../../vistas/admin/ciclos/verCiclos.php");
            exit;
        }
        $_SESSION['error'] = "No se pudo actualizar el ciclo.";
    } else {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_ciclos'] = $_POST;
    }

    header("Location: ../../../vistas/admin/ciclos/modificarCiclos.php?idCiclo=" . $idCiclo);
    exit;
}

header("Location: ../../../vistas/admin/ciclos/verCiclos.php");
exit;
?>
