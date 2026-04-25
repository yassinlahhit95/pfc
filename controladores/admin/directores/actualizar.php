<?php
session_start();
require_once "../../../modelos/directores.php";

if (isset($_POST['actualizarDirector'])) {
    $id_director = $_POST['idDirector'];
    $nombre = trim($_POST['nombreDirector']);
    $email = trim($_POST['emailDirector']);
    $dni = trim($_POST['dniDirector']);
    $telefono = trim($_POST['telefonoDirector']);
    
    // Recuperamos la fecha de alta original o la mantenemos si no se cambia
    // En este caso, el formulario no la pide, así que la recuperamos del objeto actual
    $director_original = obtenerDirectorPorId($id_director);
    $fechaAlta = $director_original['fechaAltaDirector'];

    $fechaNacimiento = $_POST['fechaNacimientoDirector'] ?? '2000-01-01';
    $direccion = trim($_POST['direccionDirector'] ?? '');
    $ciudad = trim($_POST['ciudadDirector'] ?? '');
    $codigoPostal = trim($_POST['codigoPostalDirector'] ?? '');
    $observaciones = trim($_POST['observacionesDirector'] ?? '');

    $lista_de_errores = array();

    if (empty($nombre)) {
        $lista_de_errores['nombreDirector'] = "El nombre es obligatorio.";
    }
    if (empty($email)) {
        $lista_de_errores['emailDirector'] = "El email es obligatorio.";
    } else if (!preg_match('/^[^@]+@[^@]+\.[^@]+$/', $email)) {
        $lista_de_errores['emailDirector'] = "El formato del email no es válido.";
    }
    if (empty($dni)) {
        $lista_de_errores['dniDirector'] = "El DNI es obligatorio.";
    }
    if (empty($telefono)) {
        $lista_de_errores['telefonoDirector'] = "El teléfono es obligatorio.";
    } else if (!is_numeric($telefono)) {
        $lista_de_errores['telefonoDirector'] = "El teléfono debe ser numérico.";
    }

    if (empty($lista_de_errores)) {
        // Signature: actualizarDirector($idDirector, $nombreDirector, $emailDirector, $dniDirector, $telefonoDirector, $fechaAlta, $fechaNacimiento, $direccion, $ciudad, $codigoPostal, $observaciones)
        $resultado = actualizarDirector($id_director, $nombre, $email, $dni, $telefono, $fechaAlta, $fechaNacimiento, $direccion, $ciudad, $codigoPostal, $observaciones);
        if ($resultado) {
            $_SESSION['exito'] = "Director actualizado correctamente.";
            header("Location: /pfc/vistas/admin/directores/verDirectores.php");
            exit;
        } else {
            $_SESSION['error'] = "Error al actualizar en la base de datos.";
        }
    } else {
        $_SESSION['errores'] = $lista_de_errores;
        $_SESSION['datos_director'] = $_POST;
    }

    header("Location: /pfc/vistas/admin/directores/modificarDirectores.php?idDirector=$id_director");
    exit;
}

header("Location: /pfc/vistas/admin/directores/verDirectores.php");
exit;
?>