<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = "Solicitud inválida.";
    header("Location: ../../../vistas/secretaria/mensajes/lista.php");
    exit;
}

$idReclamacion = (int)($_POST['idReclamacion'] ?? 0);
$respuesta     = trim($_POST['respuesta'] ?? '');

if ($idReclamacion <= 0 || $respuesta === '') {
    $_SESSION['errores'] = "Datos incompletos.";
    header("Location: ../../../vistas/secretaria/mensajes/ver.php?id=$idReclamacion");
    exit;
}

$msg = obtenerMensajePorId($idReclamacion);
if (!$msg) {
    header("Location: ../../../vistas/secretaria/mensajes/lista.php");
    exit;
}

// Respuestas de secretaria se insertan como 'admin' para ser visibles en el hilo
$ok = insertarRespuestaMensaje(
    $idReclamacion,
    $msg['idEstudiante'] ? (int)$msg['idEstudiante'] : null,
    $msg['idProfesor']   ? (int)$msg['idProfesor']   : null,
    $respuesta,
    'admin'
);

if ($ok) {
    marcarMensajeComoLeido($idReclamacion);
    $_SESSION['exito'] = "Respuesta enviada.";
} else {
    $_SESSION['errores'] = "No se pudo guardar la respuesta.";
}

header("Location: ../../../vistas/secretaria/mensajes/ver.php?id=$idReclamacion");
exit;
