<?php
session_start();
require_once "../../modelos/retos.php";

if (isset($_POST['guardarReto'])) {
    // Normalización
    $nombre = trim($_POST['nombreReto']);
    $fInicio = $_POST['fechaInicio'];
    $fFin = $_POST['fechaFin'];
    $horas = $_POST['horasReto'];
    $modulos = isset($_POST['modulos']) ? $_POST['modulos'] : [];

    // Validación
    if (strlen($nombre) == 0 || !preg_match('/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]+$/', $nombre)) {
        $_SESSION['error'] = "El nombre del reto no es válido.";
        header("Location: ../../vistas/retos/agregarRetos.php");
        exit;
    }

    if (!ctype_digit($horas)) {
        $_SESSION['error'] = "Las horas deben ser un número.";
        header("Location: ../../vistas/retos/agregarRetos.php");
        exit;
    }

    if (empty($modulos)) {
        $_SESSION['error'] = "Debe seleccionar al menos un módulo.";
        header("Location: ../../vistas/retos/agregarRetos.php");
        exit;
    }

    // Ejecución
    $idReto = insertarReto($nombre, $fInicio, $fFin, $horas);

    if ($idReto) {
        foreach ($modulos as $idModulo) {
            asociarModuloReto($idModulo, $idReto);
        }
        $_SESSION['mensaje'] = "Reto guardado con éxito.";
    } else {
        $_SESSION['error'] = "No se ha podido guardar el reto.";
    }
}

header("Location: ../../vistas/retos/verRetos.php");
exit;
?>
