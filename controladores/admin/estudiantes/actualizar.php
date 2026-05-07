<?php
session_start();
require_once __DIR__ . "/../../../modelos/estudiantes.php";

// Verificamos si se ha enviado el formulario de actualización de estudiante
if (isset($_POST['actualizarEstudiante'])) {
    // Recolectamos y saneamos los datos del formulario
    $idEstudiante = trim($_POST['idEstudiante']);
    $nombre = trim($_POST['nombreEstudiante']);
    $email = trim($_POST['emailEstudiante']);
    $dni = trim($_POST['dniEstudiante']);
    $telefono = trim($_POST['telefonoEstudiante']);
    $fechaNacimiento = trim($_POST['fechaNacimientoEstudiante']);
    $fechaAlta = trim($_POST['fechaAltaEstudiante']);
    $direccion = trim($_POST['direccionEstudiante']);
    $ciudad = trim($_POST['ciudadEstudiante']);
    $codigoPostal = trim($_POST['codigoPostalEstudiante']);
    $observaciones = trim($_POST['observacionesEstudiante']);
    $idCiclo = trim($_POST['idCiclo']);

    // Si no hay ID de estudiante, no podemos continuar y redirigimos a la lista
    if (empty($idEstudiante)) {
        header("Location: ../../../vistas/admin/estudiantes/verEstudiantes.php");
        exit;
    }

    $errores = [];

    // Validaciones de los campos obligatorios y formatos correctos
    if (empty($nombre)) {
        $errores['nombreEstudiante'] = "El nombre es obligatorio.";
    }
    if (empty($email)) {
        $errores['emailEstudiante'] = "El email es obligatorio.";
    } elseif (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
        $errores['emailEstudiante'] = "El formato del email no es válido.";
    }
    if (empty($dni)) {
        $errores['dniEstudiante'] = "El DNI es obligatorio.";
    }
    if (empty($telefono)) {
        $errores['telefonoEstudiante'] = "El teléfono es obligatorio.";
    } elseif (!is_numeric($telefono) || !preg_match('/^[0-9]{9}$/', $telefono)) {
        $errores['telefonoEstudiante'] = "El teléfono debe ser numérico y tener exactamente 9 dígitos.";
    }
    if (empty($idCiclo)) {
        $errores['idCiclo'] = "Debe seleccionar un ciclo.";
    }

    // Antes de actualizar, verificamos que el DNI o Email no pertenezcan ya a otro estudiante
    if (empty($errores)) {
        if (checkEstudianteExistente($dni, $email, $idEstudiante)) {
            $errores['dniEstudiante'] = "El DNI o Email ya están registrados por otro estudiante.";
        }
    }

    // Si todas las validaciones son correctas, procedemos con la actualización en la base de datos
    if (empty($errores)) {
        $resultado = actualizarEstudiante($idEstudiante, $nombre, $email, $telefono, $fechaNacimiento, $dni, $fechaAlta, $direccion, $ciudad, $codigoPostal, $observaciones, $idCiclo);
        
        if ($resultado) {
            $_SESSION['exito'] = "Datos del estudiante actualizados correctamente.";
            header("Location: ../../../vistas/admin/estudiantes/verEstudiantes.php");
            exit;
        }
        
        // Error en la ejecución de la consulta de actualización
        $_SESSION['error'] = "Hubo un problema técnico al intentar actualizar los datos del estudiante. Por favor, inténtelo de nuevo.";
    } else {
        // Almacenamos los errores y los datos enviados para repoblar el formulario
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_estudiante'] = $_POST;
    }

    // Redireccionamos al formulario de modificación para mostrar errores o reintentar
    header("Location: ../../../vistas/admin/estudiantes/modificarEstudiantes.php?idEstudiante=$idEstudiante");
    exit;
}

// Redirección por defecto si se accede al script de forma directa
header("Location: ../../../vistas/admin/estudiantes/verEstudiantes.php");
exit;
?>
