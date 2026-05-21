<?php
session_start();
require_once __DIR__ . "/../../../modelos/retos.php";

$idEstudiante = $_POST['idEstudiante'] ?? 0;
$idReto = $_POST['idReto'] ?? 0;
$idCiclo = $_POST['idCiclo'] ?? 0;
$nota = trim($_POST['nota'] ?? '');
$nota = str_replace(',', '.', $nota);

if ($idEstudiante && $idReto) {
    if ($nota === '') {
        eliminarCalificacionReto($idEstudiante, $idReto);
        $_SESSION['exito'] = "Nota eliminada.";
    } elseif (!is_numeric($nota) || $nota < 0 || $nota > 10) {
        $_SESSION['errores'] = "La nota debe ser un número entre 0 y 10.";
    } else {
        if (calificarReto($idEstudiante, $idReto, floatval($nota))) {
            $_SESSION['exito'] = "Nota guardada correctamente.";
        } else {
            $_SESSION['errores'] = "Error al guardar la nota.";
        }
    }
} else {
    $_SESSION['errores'] = "Datos incorrectos.";
}

header("Location: ../../../vistas/admin/academico/evaluarReto.php?idEstudiante={$idEstudiante}&idReto={$idReto}&idCiclo={$idCiclo}");
exit;
?>
