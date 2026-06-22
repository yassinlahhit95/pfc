<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/directores.php";
require_once __DIR__ . "/../../../modelos/log.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['actualizarDirector'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/admin/directores/verDirectores.php");
        exit;
    }
    $idDirector      = (int)($_POST['idDirector'] ?? 0);
    $nombre          = trim($_POST['nombreDirector']);
    $email           = trim($_POST['emailDirector']);
    $dni             = trim($_POST['dniDirector']);
    $telefono        = trim($_POST['telefonoDirector']);
    $directorOriginal = obtenerDirectorPorId($idDirector);
    $fechaAlta       = $directorOriginal['fechaAltaDirector'];
    $fechaNacimiento = trim($_POST['fechaNacimientoDirector'] ?? '2000-01-01');
    $direccion       = trim($_POST['direccionDirector']);
    $ciudad          = trim($_POST['ciudadDirector']);
    $codigoPostal    = trim($_POST['codigoPostalDirector']);
    $observaciones   = trim($_POST['observacionesDirector']);

    $avisos = [];
    if (empty($nombre)) $avisos['nombreDirector'] = "El nombre es un campo obligatorio.";
    if (empty($email)) {
        $avisos['emailDirector'] = "El correo electrónico es un campo obligatorio.";
    } elseif (!Security::validateEmail($email)) {
        $avisos['emailDirector'] = "El formato del correo electrónico no es válido.";
    }
    if (empty($dni)) $avisos['dniDirector'] = "El Documento Nacional de Identidad (DNI) es un campo obligatorio.";
    if (empty($telefono)) {
        $avisos['telefonoDirector'] = "El número de teléfono es un campo obligatorio.";
    } elseif (!Security::validatePhone($telefono)) {
        $avisos['telefonoDirector'] = "El número de teléfono introducido no es válido.";
    }
    if (!empty($codigoPostal) && !is_numeric($codigoPostal)) {
        $avisos['codigoPostalDirector'] = "El código postal debe ser un valor numérico.";
    }

    if (empty($avisos) && checkDirectorExistente($dni, $email, $idDirector)) {
        $avisos['dniDirector'] = "El DNI o correo electrónico especificados ya se encuentran registrados por otro director.";
    }

    if (!empty($avisos)) {
        $_SESSION['errores'] = $avisos;
        $_SESSION['datos_director'] = $_POST;
        header("Location: ../../../vistas/admin/directores/modificarDirectores.php?idDirector=$idDirector");
        exit;
    }

    if (actualizarDirector($idDirector, $nombre, $email, $dni, $telefono, $fechaAlta, $fechaNacimiento, $direccion, $ciudad, $codigoPostal, $observaciones)) {
        registrarAccion('actualizar', 'directores', $idDirector, $nombre);
        $_SESSION['exito'] = "El director ha sido actualizado correctamente.";
        header("Location: ../../../vistas/admin/directores/verDirectores.php");
        exit;
    }
    $_SESSION['errores'] = "Ocurrió un error al intentar actualizar la información del director.";
    header("Location: ../../../vistas/admin/directores/modificarDirectores.php?idDirector=$idDirector");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/admin/directores/verDirectores.php");
exit;
