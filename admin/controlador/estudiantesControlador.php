<?php
session_start();
require_once "../modelos/conexion.php";
require_once "../modelos/estudiantes.php";

$con = new Conexion();
$conexion = $con->conectar();
$modelo = new estudiante($conexion);

if (isset($_POST['guardarEstudiante'])) {
    $accion = $_POST['accion'];
    
    unset($_SESSION['errores']);
    unset($_SESSION['datos_estudiante']);

    $nombre = trim($_POST['nombreEstudiante']);
    $email = trim($_POST['emailEstudiante']);
    $dni = trim($_POST['dniEstudiante']);
    $telefono = trim($_POST['telefonoEstudiante']);
    $fechaNacimiento = $_POST['fechaNacimientoEstudiante'];
    $fechaAlta = $_POST['fechaAltaEstudiante'];
    $direccion = trim($_POST['direccionEstudiante']);
    $ciudad = trim($_POST['ciudadEstudiante']);
    $codigoPostal = trim($_POST['codigoPostalEstudiante']);
    $nivel = trim($_POST['nivelEstudiante']);
    $idCurso = $_POST['idCurso'];
    $idEstado = $_POST['idEstado'];
    $observaciones = trim($_POST['observacionesEstudiante']);

    $errores = [];

    if (empty($nombre)) $errores['nombreEstudiante'] = "El nombre es obligatorio";
    if (empty($email)) $errores['emailEstudiante'] = "El email es obligatorio";
    if (empty($dni)) $errores['dniEstudiante'] = "El DNI es obligatorio";
    if (empty($telefono)) $errores['telefonoEstudiante'] = "El teléfono es obligatorio";
    if (empty($fechaNacimiento)) $errores['fechaNacimientoEstudiante'] = "La fecha de nacimiento es obligatoria";
    if (empty($fechaAlta)) $errores['fechaAltaEstudiante'] = "La fecha de alta es obligatoria";
    if (empty($direccion)) $errores['direccionEstudiante'] = "La dirección es obligatoria";
    if (empty($ciudad)) $errores['ciudadEstudiante'] = "La ciudad es obligatoria";
    if (empty($codigoPostal)) $errores['codigoPostalEstudiante'] = "El código postal es obligatorio";
    if (empty($nivel)) $errores['nivelEstudiante'] = "El nivel es obligatorio";
    if (empty($idCurso)) $errores['idCurso'] = "El curso es obligatorio";
    if (empty($idEstado)) $errores['idEstado'] = "El estado es obligatorio";

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_estudiante'] = $_POST;
        $url = ($accion == 'insertar') ? "agregarEstudiantes.php" : "modificarEstudiantes.php?id=" . $_POST['idEstudiante'];
        header("Location: ../vistas/estudiantes/" . $url);
        exit;
    }

    $datos = [
        'nombreEstudiante' => $nombre,
        'emailEstudiante' => $email,
        'telefonoEstudiante' => $telefono,
        'fechaNacimientoEstudiante' => $fechaNacimiento,
        'dniEstudiante' => $dni,
        'fechaAltaEstudiante' => $fechaAlta,
        'direccionEstudiante' => $direccion,
        'ciudadEstudiante' => $ciudad,
        'codigoPostalEstudiante' => $codigoPostal,
        'nivelEstudiante' => $nivel,
        'observacionesEstudiante' => $observaciones,
        'idCurso' => $idCurso,
        'idEstado' => $idEstado
    ];

    if ($accion == 'insertar') {
        $modelo->insertarEstudianteModelo($datos);
        $_SESSION['exito'] = "Estudiante registrado";
    } else {
        $datos['idEstudiante'] = $_POST['idEstudiante'];
        $modelo->actualizarEstudianteModelo($datos);
        $_SESSION['exito'] = "Estudiante actualizado";
    }

    header("Location: ../vistas/estudiantes/verEstudiantes.php");
    exit;
}

if (isset($_POST['accion']) && $_POST['accion'] == 'eliminar') {
    $id = $_POST['idEstudiante'];
    $modelo->eliminarEstudianteModelo($id);
    $_SESSION['exito'] = "Estudiante borrado";
    header("Location: ../vistas/estudiantes/verEstudiantes.php");
    exit;
}
?>
