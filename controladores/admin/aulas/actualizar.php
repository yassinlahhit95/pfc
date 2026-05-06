<?php
session_start();
require_once __DIR__ . "/../../../modelos/aulas.php";

if (isset($_POST['actualizarAula'])) {
    $idAula = trim($_POST['idAula']);
    $nuevoNombre = trim($_POST['nombreAula']);

    $errores = [];
    if (empty($nuevoNombre)) {
        $errores['nombreAula'] = "El nombre del aula es obligatorio.";
    }

    // Comprobamos duplicados
    if (empty($errores)) {
        if (checkAulaExistente($nuevoNombre, $idAula)) {
            $errores['nombreAula'] = "Este nombre de aula ya está en uso.";
        }
    }

    if (empty($errores)) {
        $resultado = actualizarAula($idAula, $nuevoNombre);
        if ($resultado) {
            $_SESSION['exito'] = "Aula actualizada.";
            header("Location: ../../../vistas/admin/aulas/verAulas.php");
            exit;
        }
        $_SESSION['error'] = "No se pudo actualizar el aula.";
    } else {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_aulas'] = $_POST;
    }

    header("Location: ../../../vistas/admin/aulas/modificarAulas.php?idAula=$idAula");
    exit;
}

header("Location: ../../../vistas/admin/aulas/verAulas.php");
exit;


