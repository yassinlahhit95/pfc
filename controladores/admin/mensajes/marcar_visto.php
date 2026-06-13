<?php
require_once __DIR__ . "/../../../include/Security.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";

if (empty($_SESSION['idAdmin'])) {
    header("Location: ../../../vistas/login.php");
    exit;
}

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
