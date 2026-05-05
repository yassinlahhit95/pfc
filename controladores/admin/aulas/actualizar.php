<?php
session_start();
require_once __DIR__ . "/../../../modelos/aulas.php";

if (isset($_POST['actualizarAula'])) {
    $idAula = trim($_POST['idAula']);
    $nuevoNombre = trim($_POST['nombreAula']);

    $hayError = false;
    if (empty($nuevoNombre)) {
        $hayError = true;
        $_SESSION['errores']['nombreAula'] = "El nombre del aula es obligatorio.";
    }

    // Comprobamos duplicados
    if (!$hayError) {
        if (checkAulaExistente($nuevoNombre, $idAula)) {
            $_SESSION['errores']['nombreAula'] = "Este nombre de aula ya está en uso.";
            $hayError = true;
        }
    }

    if (!$hayError) {
        $resultado = actualizarAula($idAula, $nuevoNombre);
        if ($resultado) {
            $_SESSION['exito'] = "Aula actualizada.";
            header("Location: ../../../vistas/admin/aulas/verAulas.php");
            exit;
        } else {
            $_SESSION['error'] = "No se pudo actualizar el aula.";
        }
    } else {
        $_SESSION['datos_aulas'] = $_POST;
    }

    header("Location: ../../../vistas/admin/aulas/modificarAulas.php?idAula=$idAula");
    exit;
}

header("Location: ../../../vistas/admin/aulas/verAulas.php");
exit;


