<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";

if (!isset($_POST['marcarVisto'])) {
    header("Location: ../../../vistas/admin/mensajes/lista.php");
    exit;
}

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = "Solicitud no válida o expirada.";
    header("Location: ../../../vistas/admin/mensajes/lista.php");
    exit;
}

$idReclamacion = (int)($_POST['idReclamacion'] ?? 0);

if ($idReclamacion > 0 && marcarMensajeComoLeido($idReclamacion)) {
    $_SESSION['exito'] = "Mensaje marcado como visto.";
} else {
    $_SESSION['errores'] = "No se pudo marcar como visto.";
}

header("Location: ../../../vistas/admin/mensajes/lista.php");
exit;
