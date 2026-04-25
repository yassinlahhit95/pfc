<?php
session_start();
require_once "../../../modelos/estudiantes.php";

if (isset($_POST['guardarEstudiante'])) {
    $nombre = trim($_POST['nombreEstudiante']);
    $email = trim($_POST['emailEstudiante']);
    $dni = trim($_POST['dniEstudiante']);
    $telefono = trim($_POST['telefonoEstudiante']);
    $fecha_nacimiento = $_POST['fechaNacimientoEstudiante'];
    $fecha_alta = $_POST['fechaAltaEstudiante'];
    $direccion = trim($_POST['direccionEstudiante']);
    $ciudad = trim($_POST['ciudadEstudiante']);
    $codigo_postal = trim($_POST['codigoPostalEstudiante']);
    $observaciones = "";
    if (isset($_POST['observacionesEstudiante'])) {
        $observaciones = trim($_POST['observacionesEstudiante']);
    }
    $id_ciclo = $_POST['idCiclo'];

    $lista_de_errores = array();

    if (empty($nombre)) {
        $lista_de_errores['nombreEstudiante'] = "El nombre es obligatorio.";
    }
    if (empty($email)) {
        $lista_de_errores['emailEstudiante'] = "El email es obligatorio.";
    } else {
        if (!preg_match('/^[^@]+@[^@]+\.[^@]+$/', $email)) {
            $lista_de_errores['emailEstudiante'] = "El formato del email no es válido.";
        }
    }
    if (empty($dni)) {
        $lista_de_errores['dniEstudiante'] = "El DNI es obligatorio.";
    }
    if (empty($telefono)) {
        $lista_de_errores['telefonoEstudiante'] = "El teléfono es obligatorio.";
    } elseif (!is_numeric($telefono) || !preg_match('/^[0-9]{9}$/', $telefono)) {
        $lista_de_errores['telefonoEstudiante'] = "El teléfono debe ser numérico y tener exactamente 9 dígitos.";
    }
    if (empty($fecha_nacimiento)) {
        $lista_de_errores['fechaNacimientoEstudiante'] = "La fecha de nacimiento es obligatoria.";
    }
    if (empty($direccion)) {
        $lista_de_errores['direccionEstudiante'] = "La dirección es obligatoria.";
    }
    if (empty($ciudad)) {
        $lista_de_errores['ciudadEstudiante'] = "La ciudad es obligatoria.";
    }
    if (empty($codigo_postal)) {
        $lista_de_errores['codigoPostalEstudiante'] = "El código postal es obligatorio.";
    }
    if (empty($id_ciclo)) {
        $lista_de_errores['idCiclo'] = "Debe seleccionar un ciclo.";
    }

    if (empty($lista_de_errores)) {
        $resultado = insertarEstudiante($nombre, $email, $telefono, $fecha_nacimiento, $dni, $fecha_alta, $direccion, $ciudad, $codigo_postal, $observaciones, $id_ciclo);
        if ($resultado) {
            $_SESSION['exito'] = "Estudiante registrado con éxito.";
            header("Location: /pfc/vistas/admin/estudiantes/verEstudiantes.php");
            exit;
        } else {
            $_SESSION['error'] = "Error al guardar en la base de datos.";
        }
    } else {
        $_SESSION['errores'] = $lista_de_errores;
        $_SESSION['datos_estudiante'] = $_POST;
    }

    header("Location: /pfc/vistas/admin/estudiantes/agregarEstudiantes.php");
    exit;
}

header("Location: /pfc/vistas/admin/estudiantes/verEstudiantes.php");
exit;
?>
tes.php");
    exit;
}

header("Location: /pfc/vistas/admin/estudiantes/verEstudiantes.php");
exit;
?>
