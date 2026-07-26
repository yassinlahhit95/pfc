<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
FeatureGuard::requirePage('feature_eventos');
require_once __DIR__ . "/../../../modelos/eventos.php";
require_once __DIR__ . "/../../../modelos/log.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/tutores.php";
require_once __DIR__ . "/../../../modelos/notificaciones.php";
require_once __DIR__ . "/../../firebase/firebase_helper.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../vistas/secretaria/eventos/agregarEvento.php");
    exit;
}

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/secretaria/eventos/agregarEvento.php"); exit;
}

$titulo      = Security::sanitize($_POST['tituloEvento'] ?? '');
$descripcion = Security::sanitize($_POST['descripcionEvento'] ?? '');
$fecha       = Security::sanitize($_POST['fechaEvento'] ?? '');
$hora        = Security::sanitize($_POST['horaEvento'] ?? '');
$ubicacion   = Security::sanitize($_POST['ubicacionEvento'] ?? '');

$errores = [];
if (empty($titulo)) $errores[] = "El título es obligatorio.";
if (empty($fecha))  $errores[] = "La fecha es obligatoria.";

if ($errores) {
    $_SESSION['errores'] = $errores;
    header("Location: ../../../vistas/secretaria/eventos/agregarEvento.php");
    exit;
}

$ok = insertarEvento($titulo, $descripcion, $fecha, $hora, $ubicacion);

if ($ok) {
    registrarAccionSecretaria('insertar', 'eventos', null, $titulo);

    // ── Notificación (in-app + push) a todos: estudiantes, profesores y tutores ──
    $mensajeNotif = "Nuevo evento: $titulo" . ($fecha ? " (" . date('d/m/Y', strtotime($fecha)) . ")" : "");
    foreach (listarIdsEstudiantesActivos() as $idDestino) {
        crearNotificacion($idDestino, 'estudiante', 'evento_nuevo', $mensajeNotif, '../../../vistas/estudiantes/eventos/lista.php');
    }
    foreach (listarIdsProfesores() as $idDestino) {
        crearNotificacion($idDestino, 'profesor', 'evento_nuevo', $mensajeNotif, '../../../vistas/profesores/eventos/lista.php');
    }
    // Tutores no tienen campana de notificaciones en su nav (a diferencia de
    // estudiante/profesor) — se les notifica solo por push, no in-app.

    $tokens = array_unique(array_merge(
        obtenerTokensEstudiantes(), obtenerTokensProfesores(), obtenerTokensTutores()
    ));
    foreach ($tokens as $token) {
        enviarNotificacionFirebase($token, "NUEVO EVENTO: $titulo", $mensajeNotif, 'evento_nuevo');
    }

    $_SESSION['exito'] = "Evento creado correctamente.";
} else {
    $_SESSION['errores'] = "Error al crear el evento.";
}
header("Location: ../../../vistas/secretaria/eventos/gestionEventos.php");
exit;
