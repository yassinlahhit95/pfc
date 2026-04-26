<?php
session_start();
require_once "../../../modelos/inventario.php";

if (isset($_POST['registrarPrestamo'])) {
    $id_articulo = $_POST['idArticulo'];
    $id_estudiante = $_POST['idEstudiante'];
    $fecha = $_POST['fechaPrestamo'];

    $lista_de_errores = [];

    if (empty($id_articulo)) {
        $lista_de_errores['idArticulo'] = "Debe seleccionar un equipo.";
    }
    if (empty($id_estudiante)) {
        $lista_de_errores['idEstudiante'] = "Debe seleccionar un estudiante.";
    }
    if (empty($fecha)) {
        $lista_de_errores['fechaPrestamo'] = "La fecha es obligatoria.";
    }

    if (empty($lista_de_errores)) {
        $resultado = registrarPrestamo($id_estudiante, $id_articulo, $fecha);
        if ($resultado) {
            $_SESSION['exito'] = "Préstamo registrado correctamente.";
            header("Location: /pfc/vistas/admin/inventario/gestionarPrestamos.php");
            exit;
        } else {
            $_SESSION['error'] = "Error al guardar en la base de datos.";
        }
    } else {
        $_SESSION['errores'] = $lista_de_errores;
        $_SESSION['datos_prestamo'] = $_POST;
    }

    header("Location: /pfc/vistas/admin/inventario/agregarPrestamo.php");
    exit;
}

header("Location: /pfc/vistas/admin/inventario/gestionarPrestamos.php");
exit;

