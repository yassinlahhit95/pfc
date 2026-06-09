<?php
require_once __DIR__ . "/../../../include/Security.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";

if (empty($_SESSION['idProfesor'])) { header("Location: ../../../vistas/login.php"); exit; }
if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = "Solicitud no válida o expirada.";
    header("Location: ../../../vistas/profesores/mensajes/lista.php"); exit;
}

$idReclamacion = intval($_POST['idReclamacion'] ?? 0);

// Seguridad: solo puede borrar mensajes dirigidos a este profesor (evita IDOR)
if (!mensajePerteneceAProfesor($idReclamacion, $_SESSION['idProfesor'])) {
    $_SESSION['errores'] = "No tienes permiso sobre este mensaje.";
    header("Location: ../../../vistas/profesores/mensajes/lista.php"); exit;
}

if (eliminarMensaje($idReclamacion)) {
    $_SESSION['exito'] = "Mensaje eliminado.";
} else {
    $_SESSION['errores'] = "Error al eliminar.";
}

header("Location: ../../../vistas/profesores/mensajes/lista.php");
exit;
