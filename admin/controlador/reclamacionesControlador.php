<?php
session_start();
require_once "../modelos/conexion.php";
require_once "../modelos/reclamaciones.php";

$objetoConexion = new Conexion();
$conexionBD = $objetoConexion->conectar();
$modeloReclamacion = new reclamacion($conexionBD);

if (isset($_POST['guardarReclamacion'])) {
    $accion = $_POST['accion'];
    
    unset($_SESSION['errores'], $_SESSION['datos_reclamaciones']);

    if ($accion == 'insertar') {
        $errores = [];
        
        $idEstudiante = $_POST['idEstudiante'] ?? '';
        $idProfesor = $_POST['idProfesor'] ?? '';
        $asunto = trim($_POST['asunto'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $gravedad = $_POST['gravedad'] ?? '';
        $fecha = $_POST['fecha'] ?? '';

        if (empty($idEstudiante)) { $errores['idEstudiante'] = "Debe seleccionar un estudiante."; }
        if (empty($idProfesor)) { $errores['idProfesor'] = "Debe seleccionar un profesor."; }
        if (empty($asunto)) { $errores['asunto'] = "El asunto es obligatorio."; }
        if (empty($descripcion)) { $errores['descripcion'] = "La descripción es obligatoria."; }
        if (empty($gravedad)) { $errores['gravedad'] = "La gravedad es obligatoria."; }
        if (empty($fecha)) { $errores['fecha'] = "La fecha es obligatoria."; }

        if (count($errores) > 0) {
            $_SESSION['errores'] = $errores;
            $_SESSION['datos_reclamaciones'] = $_POST;
            header("Location: ../vistas/reclamaciones/agregarReclamacion.php");
            exit;
        }

        $datos = [
            'idEstudiante' => $idEstudiante,
            'idProfesor' => $idProfesor,
            'asunto' => $asunto,
            'descripcion' => $descripcion,
            'gravedad' => $gravedad,
            'fecha' => $fecha
        ];

        if ($modeloReclamacion->insertarReclamacionModelo($datos)) {
            $_SESSION['exito'] = "Reclamación registrada correctamente.";
        }
        header("Location: ../vistas/reclamaciones/verReclamaciones.php");
        exit;
    }
}

if (isset($_POST['accion'])) {
    $accion = $_POST['accion'];

    // Cambiar el estado (Pendiente / Resuelta)
    if ($accion == 'cambiar_estado') {
        $id = $_POST['idReclamacion'];
        $nuevoEstado = $_POST['nuevo_estado'];

        if ($modeloReclamacion->cambiarEstadoModelo($id, $nuevoEstado)) {
            $_SESSION['exito'] = "Estado actualizado correctamente.";
        }
        header("Location: ../vistas/reclamaciones/verReclamaciones.php");
        exit;

    } 
    
    // Eliminar reclamación
    else if ($accion == 'eliminar') {
        $id = $_POST['idReclamacion'];
        if ($modeloReclamacion->eliminarReclamacionModelo($id)) {
            $_SESSION['exito'] = "Reclamación eliminada.";
        }
        header("Location: ../vistas/reclamaciones/verReclamaciones.php");
        exit;
    }
}
?>