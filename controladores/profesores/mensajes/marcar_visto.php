<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";

if (isset($_POST['marcarVisto'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = "Solicitud no válida o expirada.";
        header("Location: ../../../vistas/profesores/mensajes/lista.php"); exit;
    }
    $idReclamacion = intval($_POST['idReclamacion'] ?? 0);

    // Seguridad: solo puede marcar como visto un mensaje dirigido a este profesor (evita IDOR)
    if (!mensajePerteneceAProfesor($idReclamacion, $_SESSION['idProfesor'])) {
        $_SESSION['errores'] = "No tienes permiso sobre este mensaje.";
        header("Location: ../../../vistas/profesores/mensajes/lista.php"); exit;
    }

    if (marcarMensajeComoLeido($idReclamacion)) {
        $_SESSION['exito'] = "Mensaje visto.";
    } else {
        $_SESSION['errores'] = "No se pudo marcar el mensaje como leído.";
    }
    header("Location: ../../../vistas/profesores/mensajes/detalles.php?id=" . $idReclamacion);
    exit;
}

header("Location: ../../../vistas/profesores/mensajes/lista.php");
exit;
