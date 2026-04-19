<?php
session_start();
require_once "../../modelos/modulos.php";

if (isset($_POST['guardarModulo'])) {
    $nombre = trim($_POST['nombreModulo'] ?? '');
    $idCiclo = $_POST['idCiclo'] ?? '';
    $horas = $_POST['horasMaximas'] ?? 0;

    if (empty($nombre) || empty($idCiclo)) {
        $_SESSION['error'] = "El nombre y el ciclo son obligatorios.";
        header("Location: ../../vistas/modulos/verModulos.php");
        exit;
    }

    if (insertarModulo($nombre, $idCiclo, $horas)) {
        $_SESSION['exito'] = "Módulo guardado con éxito.";
    } else {
        $_SESSION['error'] = "No se ha podido guardar el módulo.";
    }
}

header("Location: ../../vistas/modulos/verModulos.php");
exit;
?>
