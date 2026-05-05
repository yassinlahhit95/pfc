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
        require_once __DIR__ . "/../../../modelos/conectar.php";
        $con = obtenerConexion();

        $sqlNombre = "SELECT idAula FROM aulas WHERE nombreAula = '" . mysqli_real_escape_string($con, $nuevoNombre) . "' AND idAula != $idAula";
        $resNombre = mysqli_query($con, $sqlNombre);
        if (mysqli_num_rows($resNombre) > 0) {
            $_SESSION['errores']['nombreAula'] = "Este nombre de aula ya está en uso.";
            $hayError = true;
        }
        mysqli_close($con);
    }

    if (!$hayError) {
        $resultado = actualizarAula($idAula, $nuevoNombre);
        if ($resultado) {
            $_SESSION['exito'] = "Aula actualizada.";
            header("Location: ../../../vistas/admin/aulas/verAulas.php");
            exit;
        } else {
            $_SESSION['error'] = "Error inesperado al actualizar.";
        }
    } else {
        $_SESSION['datos_aulas'] = $_POST;
    }

    header("Location: ../../../vistas/admin/aulas/modificarAulas.php?idAula=$idAula");
    exit;
}

header("Location: ../../../vistas/admin/aulas/verAulas.php");
exit;
