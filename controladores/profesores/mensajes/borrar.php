<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$idReclamacion = intval($_POST['idReclamacion'] ?? 0);

// Seguridad: solo puede borrar mensajes dirigidos a este profesor (evita IDOR)
if (!mensajePerteneceAProfesor($idReclamacion, $_SESSION['idProfesor'])) {
    $_SESSION['errores'] = "No tienes permiso sobre este mensaje.";
    header("Location: ../../../vistas/profesores/mensajes/lista.php"); exit;
}

if (eliminarMensaje($idReclamacion)) {
    $_SESSION['exito'] = "Mensaje eliminado.";
} else {
    $_SESSION['errores'] = "No se pudo eliminar el mensaje.";
}

header("Location: ../../../vistas/profesores/mensajes/lista.php");
exit;
