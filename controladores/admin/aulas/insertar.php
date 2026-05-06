<?php
session_start();
require_once __DIR__ . "/../../../modelos/aulas.php";

if (isset($_POST['guardarAula'])) {
    $nombreAula = trim($_POST['nombreAula']);

    $errores = [];
    if (empty($nombreAula)) {
        $errores['nombreAula'] = "El nombre del aula es obligatorio.";
    }

    // Comprobamos duplicados
    if (empty($errores)) {
        if (checkAulaExistente($nombreAula)) {
            $errores['nombreAula'] = "Este nombre de aula ya existe.";
        }
    }

    if (empty($errores)) {
        $resultado = insertarAula($nombreAula);
        if ($resultado) {
            $_SESSION['exito'] = "Aula registrada.";
            header("Location: ../../../vistas/admin/aulas/verAulas.php");
            exit;
        }
        $_SESSION['error'] = "No se pudo registrar el aula.";
    } else {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_aulas'] = $_POST;
    }

    header("Location: ../../../vistas/admin/aulas/verAulas.php");
    exit;
}

header("Location: ../../../vistas/admin/aulas/verAulas.php");
exit;
?>
