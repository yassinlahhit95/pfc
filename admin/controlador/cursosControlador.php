<?php
session_start();
require_once "../modelos/conexion.php";
require_once "../modelos/cursos.php";

$con = new Conexion();
$conexion = $con->conectar();
$modelo = new curso($conexion);

if (isset($_POST['guardarCurso'])) {
    $accion = $_POST['accion'];
    
    unset($_SESSION['errores']);
    unset($_SESSION['datos_curso']);

    $nombre = trim($_POST['nombreCurso']);
    $idNivel = $_POST['idNivel'];
    $idProfesor = $_POST['idProfesor'];
    $idAula = $_POST['idAula'];
    $idEstado = $_POST['idEstado'];
    $idCiclo = $_POST['idCiclo'];
    $descripcion = trim($_POST['descripcionCurso']);
    
    $errores = [];

    if (!isset($_POST['nombreCurso']) || empty($nombre)) {
        $errores['nombreCurso'] = "El nombre del curso es obligatorio";
    }
    if (!isset($_POST['idNivel']) || empty($idNivel)) {
        $errores['idNivel'] = "El nivel académico es obligatorio";
    }
    if (!isset($_POST['idProfesor']) || empty($idProfesor)) {
        $errores['idProfesor'] = "El profesor tutor es obligatorio";
    }
    if (!isset($_POST['idAula']) || empty($idAula)) {
        $errores['idAula'] = "El aula es obligatoria";
    }
    if (!isset($_POST['idEstado']) || empty($idEstado)) {
        $errores['idEstado'] = "El estado es obligatorio";
    }
    if (!isset($_POST['idCiclo']) || empty($idCiclo)) {
        $errores['idCiclo'] = "El ciclo académico es obligatorio";
    }
    if (!isset($_POST['descripcionCurso']) || empty($descripcion)) {
        $errores['descripcionCurso'] = "La descripción es obligatoria";
    }

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_curso'] = $_POST;
        $url = ($accion == 'insertar') ? "agregarCursos.php" : "modificarCursos.php?id=" . $_POST['idCurso'];
        header("Location: ../vistas/cursos/" . $url);
        exit;
    }

    $datos = [
        'nombreCurso' => $nombre,
        'descripcionCurso' => $descripcion,
        'idNivel' => $idNivel,
        'idProfesor' => $idProfesor,
        'idAula' => $idAula,
        'idEstado' => $idEstado,
        'idCiclo' => $idCiclo
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

if (isset($_POST['accion']) && $_POST['accion'] == 'eliminar') {
        $id = $_POST['idCurso'];
        $modelo->eliminarCursoModelo($id);
        $_SESSION['exito'] = "Curso borrado";
        header("Location: ../vistas/cursos/verCursos.php");
        exit;
    }
}
?>