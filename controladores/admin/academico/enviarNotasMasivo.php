<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../comunes/notificaciones_grades.php";
require_once __DIR__ . "/../../../modelos/log.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$hayError = false;

if (isset($_REQUEST['idCiclo']) && !empty($_REQUEST['idCiclo'])) {
    $idCiclo = trim($_REQUEST['idCiclo']);

    require_once __DIR__ . "/../../../modelos/estudiantes.php";
    $estudiantesEnCiclo = listarEstudiantesPorCiclo($idCiclo);

    if (empty($estudiantesEnCiclo)) {
        $_SESSION['errores'] = "No se han encontrado estudiantes matriculados en este ciclo formativo para efectuar el envío de notas.";
    } else {
        $encolados = encolarEmailsNotasClase((int)$idCiclo);

        if ($encolados > 0) {
            registrarAccion('enviar_notas_masivo', 'ciclos', (int)$idCiclo, "$encolados emails encolados");
            $_SESSION['exito'] = "Se han encolado $encolados correos electrónicos. El envío se realizará automáticamente en los próximos minutos.";
        } else {
            $_SESSION['errores'] = "No se pudieron encolar los correos electrónicos. Por favor, verifique la configuración del servidor de correo saliente.";
        }
    }
} else {
    $_SESSION['errores'] = "El identificador del ciclo formativo no ha sido proporcionado.";
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/admin/academico/resultadosFinales.php?idCiclo=" . ($idCiclo ?? ''));
exit;
