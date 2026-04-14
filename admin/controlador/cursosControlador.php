<?php
session_start();
require_once "../modelos/conexion.php";
require_once "../modelos/cursos.php";

$con = new Conexion();
$conexion = $con->conectar();
$modelo = new curso($conexion);

if (isset($_POST['accion'])) {
    $accion = $_POST['accion'];
    
    unset($_SESSION['error_nombre']);
    unset($_SESSION['datos_curso']);

    if ($accion == 'insertar' || $accion == 'actualizar') {
        
        $nombre = trim($_POST['nombreCurso']);

        if ($nombre == "") {
            $_SESSION['error_nombre'] = "Escribe el nombre del curso";
            $_SESSION['datos_curso'] = $_POST;
            $url = ($accion == 'insertar') ? "agregarCursos.php" : "modificarCursos.php?id=" . $_POST['idCurso'];
            header("Location: ../vistas/cursos/" . $url);
            exit;
        }

        $datos = [
            'nombreCurso' => $nombre,
            'descripcionCurso' => $_POST['descripcionCurso'],
            'idNivel' => $_POST['idNivel'],
            'idProfesor' => $_POST['idProfesor'],
            'idAula' => $_POST['idAula'],
            'idEstado' => $_POST['idEstado']
        ];

        if ($accion == 'insertar') {
            $modelo->insertarCursoModelo($datos);
            $_SESSION['exito'] = "Curso creado";
        } else {
            $datos['idCurso'] = $_POST['idCurso'];
            $modelo->actualizarCursoModelo($datos);
            $_SESSION['exito'] = "Curso actualizado";
        }

        header("Location: ../vistas/cursos/verCursos.php");
        exit;
    }

    if ($accion == 'eliminar') {
        $id = $_POST['idCurso'];
        $modelo->eliminarCursoModelo($id);
        $_SESSION['exito'] = "Curso borrado";
        header("Location: ../vistas/cursos/verCursos.php");
        exit;
    }
}
?>