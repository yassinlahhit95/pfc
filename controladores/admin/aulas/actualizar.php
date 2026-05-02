<?php
session_start();
require_once __DIR__ . "/../../../modelos/aulas.php";

if (isset($_POST['actualizarAula'])) {
    $idAula = trim($_POST['idAula']);
    $nombreAula = trim($_POST['nombreAula']);

    $hayError = false;
    if (empty($nombreAula)) {
        $hayError = true;
        $_SESSION['error'] = "El nombre del aula es obligatorio.";
    }

    if (!$hayError) {
        $resultado = actualizarAula($idAula, $nombreAula);
        if ($resultado) {
            $_SESSION['exito'] = "Aula actualizada.";
            header("Location: ../../../vistas/admin/aulas/verAulas.php");
            exit;
        } else {
            $_SESSION['error'] = "Error al actualizar el aula.";
        }
    } else {
        $_SESSION['datos_aulas'] = $_POST;
    }

    header("Location: ../../../vistas/admin/aulas/modificarAulas.php?idAula=$idAula");
    exit;
}

header("Location: ../../../vistas/admin/aulas/verAulas.php");
exit;
