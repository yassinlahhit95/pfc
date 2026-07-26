<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/log.php";
require_once __DIR__ . "/../../../modelos/notificaciones.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['actualizarProfesor'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/admin/profesores/verProfesores.php");
        exit;
    }

    $idProfesor      = (int)($_POST['idProfesor'] ?? 0);
    $nombre          = trim($_POST['nombreProfesor']);
    $email           = trim($_POST['emailProfesor']);
    $dni             = trim($_POST['dniProfesor']);
    $telefono        = trim($_POST['telefonoProfesor']);
    $direccion       = trim($_POST['direccionProfesor']);
    $fechaNacimiento = trim($_POST['fechaNacimientoProfesor'] ?? '');
    $fechaAlta       = trim($_POST['fechaAltaProfesor'] ?? '') ?: date('Y-m-d');
    $ciudad          = trim($_POST['ciudadProfesor']);
    $codigoPostal    = trim($_POST['codigoPostalProfesor']);
    $observaciones   = trim($_POST['observacionesProfesor']);

    $errores = [];
    if (empty($nombre)) $errores['nombreProfesor'] = "El nombre es un campo obligatorio.";
    if (empty($email)) {
        $errores['emailProfesor'] = "El correo electrónico es un campo obligatorio.";
    } elseif (!Security::validateEmail($email)) {
        $errores['emailProfesor'] = "El formato del correo electrónico no es válido.";
    }
    if (empty($dni)) $errores['dniProfesor'] = "El Documento Nacional de Identidad (DNI) es un campo obligatorio.";
    if (empty($telefono)) {
        $errores['telefonoProfesor'] = "El número de teléfono es un campo obligatorio.";
    } elseif (!Security::validatePhone($telefono)) {
        $errores['telefonoProfesor'] = "El número de teléfono introducido no es válido.";
    }
    if (empty($direccion)) $errores['direccionProfesor'] = "La dirección es un campo obligatorio.";
    if (empty($ciudad)) $errores['ciudadProfesor'] = "La ciudad es un campo obligatorio.";
    if (empty($codigoPostal)) {
        $errores['codigoPostalProfesor'] = "El código postal es un campo obligatorio.";
    } elseif (!is_numeric($codigoPostal)) {
        $errores['codigoPostalProfesor'] = "El código postal debe ser un valor numérico.";
    }
    if (empty($fechaNacimiento)) $errores['fechaNacimientoProfesor'] = "La fecha de nacimiento es un campo obligatorio.";

    if (empty($errores) && checkProfesorExistente($dni, $email, $idProfesor)) {
        $errores['dniProfesor'] = "El DNI o correo electrónico especificados ya se encuentran registrados por otro profesor.";
    }

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_profesor'] = $_POST;
        header("Location: ../../../vistas/admin/profesores/modificarProfesores.php?idProfesor=$idProfesor");
        exit;
    }

    if (actualizarProfesor($idProfesor, $nombre, $email, $telefono, $dni, $direccion, $fechaNacimiento, $fechaAlta, $ciudad, $codigoPostal, $observaciones)) {
        registrarAccion('actualizar', 'profesores', $idProfesor, $nombre);
        // Tutor status
        $esTutor = !empty($_POST['esTutor']) ? 1 : 0;
        $idCicloTutor = $esTutor ? (int)($_POST['idCicloTutor'] ?? 0) : 0;
        actualizarTutorStatus($idProfesor, $esTutor, $idCicloTutor ?: null);

        // Snapshot ANTES de limpiar/reinsertar, para notificar al profesor
        // solo de las asignaciones realmente nuevas (ver comentario en
        // obtenerIdsCiclosDirectosProfesor()).
        $ciclosAntes  = obtenerIdsCiclosDirectosProfesor($idProfesor);
        $modulosAntes = obtenerIdsModulosDirectosProfesor($idProfesor);

        limpiarCiclosProfesor($idProfesor);
        $ciclosSeleccionados = [];
        if (isset($_POST['ciclos']) && is_array($_POST['ciclos'])) {
            foreach ($_POST['ciclos'] as $idCic) {
                $idCic = (int)$idCic;
                if ($idCic > 0) {
                    asociarCicloProfesor($idCic, $idProfesor);
                    $ciclosSeleccionados[] = $idCic;
                }
            }
        }
        limpiarModulosProfesor($idProfesor);
        $modulosSeleccionados = [];
        if (isset($_POST['modulos']) && is_array($_POST['modulos'])) {
            foreach ($_POST['modulos'] as $idMod) {
                $idMod = (int)$idMod;
                if ($idMod > 0) {
                    asociarModuloProfesor($idMod, $idProfesor);
                    $modulosSeleccionados[] = $idMod;
                }
            }
        }

        foreach (array_diff($ciclosSeleccionados, $ciclosAntes) as $idCicNuevo) {
            $cicloInfo = obtenerCicloPorId($idCicNuevo);
            if ($cicloInfo) {
                crearNotificacion($idProfesor, 'profesor', 'ciclo_asignado',
                    'Se te ha asignado un nuevo ciclo: ' . $cicloInfo['nombreCiclo'],
                    '../../../vistas/profesores/inicio/dashboard.php');
            }
        }
        foreach (array_diff($modulosSeleccionados, $modulosAntes) as $idModNuevo) {
            $moduloInfo = obtenerModuloPorId($idModNuevo);
            if ($moduloInfo) {
                crearNotificacion($idProfesor, 'profesor', 'modulo_asignado',
                    'Se te ha asignado un nuevo módulo: ' . $moduloInfo['nombreModulo'],
                    '../../../vistas/profesores/inicio/dashboard.php');
            }
        }

        $_SESSION['exito'] = "El profesor ha sido actualizado correctamente.";
        header("Location: ../../../vistas/admin/profesores/verProfesores.php");
        exit;
    }
    $_SESSION['errores'] = "Ocurrió un error al intentar actualizar la información del profesor o no se detectaron cambios.";
    header("Location: ../../../vistas/admin/profesores/modificarProfesores.php?idProfesor=$idProfesor");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/admin/profesores/verProfesores.php");
exit;
