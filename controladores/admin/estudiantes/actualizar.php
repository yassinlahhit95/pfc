<?php
session_start();
require_once "../../../modelos/estudiantes.php";

if (isset($_POST['actualizarEstudiante'])) {
    $id_estudiante = $_POST['idEstudiante'];
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

    if (empty($id_estudiante)) {
        header("Location: /pfc/vistas/admin/estudiantes/verEstudiantes.php");
        exit;
    }

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
    if (empty($id_ciclo)) {
        $lista_de_errores['idCiclo'] = "Debe seleccionar un ciclo.";
    }

    if (empty($lista_de_errores)) {
        $resultado = actualizarEstudiante($id_estudiante, $nombre, $email, $telefono, $fecha_nacimiento, $dni, $fecha_alta, $direccion, $ciudad, $codigo_postal, $observaciones, $id_ciclo);
        if ($resultado) {
            $_SESSION['exito'] = "Actualizado correctamente.";
            header("Location: /pfc/vistas/admin/estudiantes/verEstudiantes.php");
            exit;
        } else {
            $_SESSION['error'] = "Error al actualizar.";
        }
    } else {
        $_SESSION['errores'] = $lista_de_errores;
        $_SESSION['datos_estudiante'] = $_POST;
    }

    header("Location: /pfc/vistas/admin/estudiantes/modificarEstudiantes.php?idEstudiante=$id_estudiante");
    exit;
}

header("Location: /pfc/vistas/admin/estudiantes/verEstudiantes.php");
exit;
?>
