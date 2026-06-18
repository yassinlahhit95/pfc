<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . "/../../../modelos/eventos.php";

if (isset($_POST['actualizarEvento'])) {
    $idEvento = (int)($_POST['idEvento'] ?? 0);
    $titulo = trim($_POST['tituloEvento']);
    $descripcion = trim($_POST['descripcionEvento']);
    $fechaEvento = trim($_POST['fechaEvento']);
    $horaEvento = trim($_POST['horaEvento']);
    $ubicacionEvento = trim($_POST['ubicacionEvento']);

    $errores = '';

    if (empty($titulo)) {
        $errores = "El título es obligatorio.";
    }
    if (empty($ubicacionEvento)) {
        $errores = "La ubicación es obligatoria.";
    }
    if (empty($fechaEvento)) {
        $errores = "La fecha es obligatoria.";
    }
    if (empty($horaEvento)) {
        $errores = "La hora es obligatoria.";
    }

    if (!$errores) {
        $resultado = actualizarEvento($idEvento, $titulo, $descripcion, $fechaEvento, $horaEvento, $ubicacionEvento);
        if ($resultado) {
            $_SESSION['exito'] = "Evento actualizado.";
            header("Location: ../../../vistas/admin/eventos/gestionEventos.php");
            exit;
        }
        $_SESSION['errores'] = "Error al actualizar.";
    } else {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_evento'] = $_POST;
    }
    
    header("Location: ../../../vistas/admin/eventos/modificarEvento.php?idEvento=" . $idEvento);
    exit;
}

header("Location: ../../../vistas/admin/eventos/gestionEventos.php");
exit;
?>
