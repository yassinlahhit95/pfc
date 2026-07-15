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
if (isset($_POST['guardarEstudiante'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/admin/estudiantes/agregarEstudiantes.php");
        exit;
    }

    $nombre          = trim($_POST['nombreEstudiante']);
    $email           = trim($_POST['emailEstudiante']);
    $dni             = trim($_POST['dniEstudiante']);
    $telefono        = trim($_POST['telefonoEstudiante']);
    $fechaNacimiento = trim($_POST['fechaNacimientoEstudiante']);
    $fechaAlta       = !empty($_POST['fechaAltaEstudiante']) ? trim($_POST['fechaAltaEstudiante']) : date('Y-m-d');
    $direccion       = trim($_POST['direccionEstudiante']);
    $ciudad          = trim($_POST['ciudadEstudiante']);
    $codigoPostal    = trim($_POST['codigoPostalEstudiante']);
    $observaciones   = trim($_POST['observacionesEstudiante']);
    $idCiclo         = (int)($_POST['idCiclo'] ?? 0);
    $cursosPermitidos = ['Grado Medio', 'Grado Superior'];
    $curso           = in_array($_POST['curso'] ?? '', $cursosPermitidos, true) ? $_POST['curso'] : '';
    $anioEstudioPost  = trim($_POST['anioEstudio'] ?? '');
    $anioEstudio      = existeNombreCursoEnCiclo($idCiclo, $anioEstudioPost) && $anioEstudioPost !== '' ? $anioEstudioPost : null;

    $avisos = [];
    if (empty($nombre)) $avisos['nombreEstudiante'] = "El nombre es un campo obligatorio.";
    if (empty($email)) {
        $avisos['emailEstudiante'] = "El correo electrónico es un campo obligatorio.";
    } elseif (!Security::validateEmail($email)) {
        $avisos['emailEstudiante'] = "El formato del correo electrónico no es válido.";
    }
    if (empty($dni)) $avisos['dniEstudiante'] = "El Documento Nacional de Identidad (DNI) es un campo obligatorio.";
    if (empty($telefono)) {
        $avisos['telefonoEstudiante'] = "El número de teléfono es un campo obligatorio.";
    } elseif (!Security::validatePhone($telefono)) {
        $avisos['telefonoEstudiante'] = "El número de teléfono introducido no es válido.";
    }
    if (empty($fechaNacimiento)) $avisos['fechaNacimientoEstudiante'] = "La fecha de nacimiento es un campo obligatorio.";
    if (empty($direccion)) $avisos['direccionEstudiante'] = "La dirección es un campo obligatorio.";
    if (empty($ciudad)) $avisos['ciudadEstudiante'] = "La ciudad es un campo obligatorio.";
    if (empty($codigoPostal)) {
        $avisos['codigoPostalEstudiante'] = "El código postal es un campo obligatorio.";
    } elseif (!is_numeric($codigoPostal)) {
        $avisos['codigoPostalEstudiante'] = "El código postal especificado no es válido.";
    }
    if (empty($curso)) $avisos['curso'] = "Debe seleccionar un nivel formativo.";
    if ($idCiclo <= 0) $avisos['idCiclo'] = "Debe seleccionar un ciclo formativo.";

    if (empty($avisos) && checkEstudianteExistente($dni, $email)) {
        $avisos['dniEstudiante'] = "El DNI o correo electrónico especificados ya se encuentran registrados.";
    }

    if (!empty($avisos)) {
        $_SESSION['errores'] = $avisos;
        $_SESSION['datos_estudiante'] = $_POST;
        header("Location: ../../../vistas/admin/estudiantes/agregarEstudiantes.php");
        exit;
    }

    if (insertarEstudiante($nombre, $email, $telefono, $fechaNacimiento, $dni, $fechaAlta, $direccion, $ciudad, $codigoPostal, $observaciones, $idCiclo, $curso, $anioEstudio)) {
        registrarAccion('insertar', 'estudiantes', null, $nombre);
        $_SESSION['exito'] = mensajeExitoConCredenciales("El estudiante ha sido registrado correctamente.");
        header("Location: ../../../vistas/admin/estudiantes/verEstudiantes.php");
        exit;
    }
    $_SESSION['errores'] = "Ocurrió un error al intentar registrar al estudiante.";
    header("Location: ../../../vistas/admin/estudiantes/agregarEstudiantes.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/admin/estudiantes/verEstudiantes.php");
exit;
