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

    $errores_campos = [];
    if (empty($nombre)) {
        $hayError = true;
        $errores_campos['nombreCiclo'] = "Nombre obligatorio.";
    } 
    if (empty($abreviatura)) {
        $hayError = true;
        $errores_campos['abreviaturaCiclo'] = "Abreviatura obligatoria.";
    }

    // Comprobamos duplicados
    if (!$hayError) {
        require_once __DIR__ . "/../../../modelos/conectar.php";
        $con = obtenerConexion();
        
        $sqlNombre = "SELECT idCiclo FROM ciclos WHERE nombreCiclo = '" . $nombre . "' AND idCiclo != $idCiclo";
        $resNombre = mysqli_query($con, $sqlNombre);
        if (mysqli_num_rows($resNombre) > 0) {
            $errores_campos['nombreCiclo'] = "Este nombre de ciclo ya está en uso.";
            $hayError = true;
        }

        $sqlAbr = "SELECT idCiclo FROM ciclos WHERE abreviaturaCiclo = '" . $abreviatura . "' AND idCiclo != $idCiclo";
        $resAbr = mysqli_query($con, $sqlAbr);
        if (mysqli_num_rows($resAbr) > 0) {
            $errores_campos['abreviaturaCiclo'] = "Esta abreviatura ya está en uso.";
            $hayError = true;
        }
        mysqli_close($con);
    }

    if (!$hayError) {
        $resultado = actualizarCicloExistente($idCiclo, $nombre, $abreviatura, $idNivelEducativo, $profesores, $aulas, $precioCiclo);
        if ($resultado) {
            $_SESSION['exito'] = "Ciclo actualizado.";
            header("Location: ../../../vistas/admin/ciclos/verCiclos.php");
            exit;
        } else {
            $_SESSION['error'] = "No se pudo actualizar el ciclo.";
        }
    } else {
        $_SESSION['errores'] = $errores_campos;
        $_SESSION['datos_ciclos'] = $_POST;
    }

    header("Location: ../../../vistas/admin/ciclos/modificarCiclos.php?idCiclo=" . $idCiclo);
    exit;
}

header("Location: ../../../vistas/admin/ciclos/verCiclos.php");
exit;


