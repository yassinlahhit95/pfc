<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_eventos');
require_once __DIR__ . "/../../../modelos/eventos.php";
require_once __DIR__ . "/../../../modelos/log.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/tutores.php";
require_once __DIR__ . "/../../../modelos/notificaciones.php";
require_once __DIR__ . "/../../firebase/firebase_helper.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['guardarEvento'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/admin/eventos/agregarEvento.php");
        exit;
    }
    $titulo          = trim($_POST['tituloEvento']);
    $descripcion     = trim($_POST['descripcionEvento']);
    $fechaEvento     = trim($_POST['fechaEvento']);
    $horaEvento      = trim($_POST['horaEvento']);
    $ubicacionEvento = trim($_POST['ubicacionEvento']);

    $errores = [];
    if (empty($titulo))          $errores['tituloEvento'] = "El título del evento es un campo obligatorio.";
    if (empty($ubicacionEvento)) $errores['ubicacionEvento'] = "La ubicación del evento es un campo obligatorio.";
    if (empty($fechaEvento))     $errores['fechaEvento'] = "La fecha del evento es un campo obligatorio.";
    if (empty($horaEvento))      $errores['horaEvento'] = "La hora del evento es un campo obligatorio.";

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_evento'] = $_POST;
        header("Location: ../../../vistas/admin/eventos/agregarEvento.php");
        exit;
    }

    if (insertarEvento($titulo, $descripcion, $fechaEvento, $horaEvento, $ubicacionEvento)) {
        registrarAccion('insertar', 'eventos', null, $titulo);

        // ── Notificación (in-app + push) a todos: estudiantes, profesores y tutores ──
        $mensajeNotif = "Nuevo evento: $titulo (" . date('d/m/Y', strtotime($fechaEvento)) . ")";
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

        $_SESSION['exito'] = "El evento ha sido creado correctamente.";
        header("Location: ../../../vistas/admin/eventos/gestionEventos.php");
        exit;
    }
    $_SESSION['errores'] = "Ocurrió un error al intentar guardar el evento.";
    header("Location: ../../../vistas/admin/eventos/agregarEvento.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/admin/eventos/gestionEventos.php");
exit;
