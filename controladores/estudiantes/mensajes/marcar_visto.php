<?php
require_once __DIR__ . "/../../../include/EstudianteGuard.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";

if (isset($_POST['marcarVisto'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = "Solicitud no válida o expirada.";
        header("Location: ../../../vistas/estudiantes/mensajes/lista.php"); exit;
    }
    $idReclamacion = intval($_POST['idReclamacion'] ?? 0);

    // Seguridad: solo puede marcar como leído un mensaje propio (evita IDOR)
    if (!mensajePerteneceAEstudiante($idReclamacion, $_SESSION['idEstudiante'])) {
        $_SESSION['errores'] = "No tienes permiso sobre este mensaje.";
        header("Location: ../../../vistas/estudiantes/mensajes/lista.php"); exit;
    }

    if (marcarMensajeComoLeido($idReclamacion)) {
        $_SESSION['exito'] = "Mensaje marcado como leído.";
    } else {
        $_SESSION['errores'] = "No se pudo marcar el mensaje como leído.";
    }
    header("Location: ../../../vistas/estudiantes/mensajes/detalles.php?id=" . $idReclamacion);
    exit;
}

header("Location: ../../../vistas/estudiantes/mensajes/lista.php");
exit;
