<?php
session_start();
require_once "../modelos/conexion.php";
require_once "../modelos/reclamaciones.php";

$objetoConexion = new Conexion();
$conexionBD = $objetoConexion->conectar();
$modeloReclamacion = new reclamacion($conexionBD);

if (isset($_POST['accion'])) {
    $accion = $_POST['accion'];

    // Insertar nueva reclamación
    if ($accion == 'insertar') {
        $datos = [
            'idEstudiante' => $_POST['idEstudiante'],
            'idProfesor' => $_POST['idProfesor'],
            'asunto' => $_POST['asunto'],
            'descripcion' => $_POST['descripcion'],
            'gravedad' => $_POST['gravedad'],
            'fecha' => $_POST['fecha']
        ];

        if ($modeloReclamacion->insertarReclamacionModelo($datos)) {
            $_SESSION['exito'] = "Reclamación registrada correctamente";
        } else {
            $_SESSION['error'] = "Error al registrar la reclamación";
        }
        header("Location: ../vistas/reclamaciones/verReclamaciones.php");
        exit;

    } 
    
    // Cambiar el estado (Pendiente / Resuelta)
    else if ($accion == 'cambiar_estado') {
        $id = $_POST['idReclamacion'];
        $nuevoEstado = $_POST['nuevo_estado'];

        if ($modeloReclamacion->cambiarEstadoModelo($id, $nuevoEstado)) {
            $_SESSION['exito'] = "Estado actualizado correctamente";
        } else {
            $_SESSION['error'] = "No se pudo cambiar el estado";
        }
        header("Location: ../vistas/reclamaciones/verReclamaciones.php");
        exit;

    } 
    
    // Eliminar reclamación
    else if ($accion == 'eliminar') {
        $id = $_POST['idReclamacion'];
        if ($modeloReclamacion->eliminarReclamacionModelo($id)) {
            $_SESSION['exito'] = "Reclamación eliminada";
        } else {
            $_SESSION['error'] = "Error al intentar eliminar";
        }
        header("Location: ../vistas/reclamaciones/verReclamaciones.php");
        exit;
    }
}
?>