<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/log.php";
require_once __DIR__ . "/../../../modelos/academico_config.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['actualizarEstudiante'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/admin/estudiantes/verEstudiantes.php");
        exit;
    }

    $idEstudiante    = (int)($_POST['idEstudiante'] ?? 0);
    $nombre          = trim($_POST['nombreEstudiante']);
    $email           = trim($_POST['emailEstudiante']);
    $dni             = trim($_POST['dniEstudiante']);
    $telefono        = trim($_POST['telefonoEstudiante']);
    $fechaNacimiento = trim($_POST['fechaNacimientoEstudiante']);
    $fechaAlta       = !empty($_POST['fechaAltaEstudiante']) ? trim($_POST['fechaAltaEstudiante']) : '';
    $direccion       = trim($_POST['direccionEstudiante']);
    $ciudad          = trim($_POST['ciudadEstudiante']);
    $codigoPostal    = trim($_POST['codigoPostalEstudiante']);
    $observaciones   = trim($_POST['observacionesEstudiante']);
    $idCiclo         = (int)($_POST['idCiclo'] ?? 0);
    $cursosPermitidos = ['Grado Medio', 'Grado Superior'];
    $curso           = in_array($_POST['curso'] ?? '', $cursosPermitidos, true) ? $_POST['curso'] : 'Grado Medio';
    $anioEstudioPost  = trim($_POST['anioEstudio'] ?? '');
    $anioEstudio      = existeNombreCursoEnCiclo($idCiclo, $anioEstudioPost) && $anioEstudioPost !== '' ? $anioEstudioPost : null;

    if ($idEstudiante <= 0) {
        header("Location: ../../../vistas/admin/estudiantes/verEstudiantes.php");
        exit;
    }

    $errores = [];
    if (empty($nombre)) $errores['nombreEstudiante'] = "El nombre es un campo obligatorio.";
    if (empty($email)) {
        $errores['emailEstudiante'] = "El correo electrónico es un campo obligatorio.";
    } elseif (!Security::validateEmail($email)) {
        $errores['emailEstudiante'] = "El formato del correo electrónico no es válido.";
    }
    if (empty($dni)) $errores['dniEstudiante'] = "El Documento Nacional de Identidad (DNI) es un campo obligatorio.";
    if (empty($telefono)) {
        $errores['telefonoEstudiante'] = "El número de teléfono es un campo obligatorio.";
    } elseif (!Security::validatePhone($telefono)) {
        $errores['telefonoEstudiante'] = "El número de teléfono introducido no es válido.";
    }
    if (!empty($codigoPostal) && !is_numeric($codigoPostal)) {
        $errores['codigoPostalEstudiante'] = "El código postal debe ser un valor numérico.";
    }
    if ($idCiclo <= 0) $errores['idCiclo'] = "Debe seleccionar un ciclo formativo.";

    if (empty($errores) && checkEstudianteExistente($dni, $email, $idEstudiante)) {
        $errores['dniEstudiante'] = "El DNI o correo electrónico especificados ya se encuentran registrados por otro estudiante.";
    }

    if (empty($errores)) {
        if (actualizarEstudiante($idEstudiante, $nombre, $email, $telefono, $fechaNacimiento, $dni, $fechaAlta, $direccion, $ciudad, $codigoPostal, $observaciones, $idCiclo, $curso, $anioEstudio)) {
            registrarAccion('actualizar', 'estudiantes', $idEstudiante, $nombre);
            $_SESSION['exito'] = "La información del estudiante ha sido actualizada correctamente.";
            header("Location: ../../../vistas/admin/estudiantes/verEstudiantes.php");
            exit;
        }
        $_SESSION['errores'] = "Ocurrió un error al intentar actualizar la información del estudiante.";
    } else {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_estudiante'] = $_POST;
    }

    header("Location: ../../../vistas/admin/estudiantes/modificarEstudiantes.php?idEstudiante=" . $idEstudiante);
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/admin/estudiantes/verEstudiantes.php");
exit;
