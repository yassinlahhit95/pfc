<?php
session_start();
require_once "../../../modelos/profesores.php";

if (isset($_POST['guardarProfesor'])) {
    $nombre = trim($_POST['nombreProfesor']);
    $email = strtolower(trim($_POST['emailProfesor']));
    $dni = strtoupper(trim($_POST['dniProfesor']));
    $telefono = trim($_POST['telefonoProfesor']);
    $direccion = trim($_POST['direccionProfesor']);
    
    $ciclos = $_POST['ciclos'];
    $modulos = $_POST['modulos'];

    if (empty($nombre)) {
        $_SESSION['error'] = "El nombre es obligatorio.";
    } else if (empty($email)) {
        $_SESSION['error'] = "El email es obligatorio.";
    } else if (empty($dni)) {
        $_SESSION['error'] = "El DNI es obligatorio.";
    } else {
        $idProfesor = insertarProfesor($nombre, $email, $telefono, $dni, $direccion);
        if ($idProfesor) {
            if (isset($ciclos) && is_array($ciclos)) {
                foreach ($ciclos as $idCiclo) {
                    asociarCicloProfesor($idCiclo, $idProfesor);
                }
            }
            if (isset($modulos) && is_array($modulos)) {
                foreach ($modulos as $idModulo) {
                    asociarModuloProfesor($idModulo, $idProfesor);
                }
            }
            $_SESSION['exito'] = "Profesor registrado con éxito.";
            header("Location: /pfc/vistas/admin/profesores/verProfesores.php");
            exit;
        } else {
            $_SESSION['error'] = "Error al guardar.";
        }
    }
    header("Location: /pfc/vistas/admin/profesores/agregarProfesores.php");
    exit;
}

header("Location: /pfc/vistas/admin/profesores/verProfesores.php");
exit;
?>