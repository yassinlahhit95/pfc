<?php
session_start();
require_once __DIR__ . "/../../../modelos/aulas.php";

if (isset($_POST['guardarAula'])) {
    $nombreAula = trim($_POST['nombreAula']);

    $hayError = false;
    if (empty($nombreAula)) {
        $hayError = true;
        $_SESSION['errores']['nombreAula'] = "El nombre del aula es obligatorio.";
    }

    // Comprobamos duplicados
    if (!$hayError) {
        if (checkAulaExistente($nombreAula)) {
            $_SESSION['errores']['nombreAula'] = "Este nombre de aula ya existe.";
            $hayError = true;
        }
    }

    if (!$hayError) {
        $resultado = insertarAula($nombreAula);
        if ($resultado) {
            $_SESSION['exito'] = "Aula registrada.";
            header("Location: ../../../vistas/admin/aulas/verAulas.php");
            exit;
        } else {
            $_SESSION['error'] = "No se pudo registrar el aula.";
        }
    } else {
        $_SESSION['datos_aulas'] = $_POST;
    }

    header("Location: ../../../vistas/admin/aulas/verAulas.php");
    exit;
}

header("Location: ../../../vistas/admin/aulas/verAulas.php");
exit;


