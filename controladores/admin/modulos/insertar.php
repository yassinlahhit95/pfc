<?php
session_start();
require_once "../../../modelos/modulos.php";

if (isset($_POST['guardarModulo'])) {
    $nombre = trim($_POST['nombreModulo']);
    $idCiclo = $_POST['idCiclo'];
    $horas = $_POST['horasMaximas'];

    if (empty($nombre)) {
        $_SESSION['error'] = "El nombre del módulo es obligatorio.";
        header("Location: /pfc/vistas/admin/modulos/verModulos.php");
    } else if (empty($idCiclo)) {
        $_SESSION['error'] = "El ciclo asociado es obligatorio.";
        header("Location: /pfc/vistas/admin/modulos/verModulos.php");
    } else if (!empty($horas) && !is_numeric($horas)) {
        $_SESSION['error'] = "Las horas deben ser un valor numérico.";
        header("Location: /pfc/vistas/admin/modulos/verModulos.php");
    } else {
        if (insertarModulo($nombre, $idCiclo, $horas)) {
            $_SESSION['exito'] = "Módulo guardado con éxito.";
        } else {
            $_SESSION['error'] = "No se ha podido guardar el módulo.";
        }
        header("Location: /pfc/vistas/admin/modulos/verModulos.php");
    }
    exit;
}

header("Location: /pfc/vistas/admin/modulos/verModulos.php");
exit;
?>