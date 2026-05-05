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
        require_once __DIR__ . "/../../../modelos/conectar.php";
        $con = obtenerConexion();
        
        $sqlNombre = "SELECT idAula FROM aulas WHERE nombreAula = '" . mysqli_real_escape_string($con, $nombreAula) . "'";
        $resNombre = mysqli_query($con, $sqlNombre);
        if (mysqli_num_rows($resNombre) > 0) {
            $_SESSION['errores']['nombreAula'] = "Este nombre de aula ya existe.";
            $hayError = true;
        }
        mysqli_close($con);
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
