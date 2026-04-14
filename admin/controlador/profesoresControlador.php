<?php
session_start();
require_once "../modelos/conexion.php";
require_once "../modelos/profesores.php";

$con = new Conexion();
$conexion = $con->conectar();
$modelo = new profesor($conexion);

if (isset($_POST['guardarProfesor'])) {
    $accion = $_POST['accion'];
    
    unset($_SESSION['errores']);
    unset($_SESSION['datos_profesor']);

    $nombre = trim($_POST['nombreProfesor']);
    $email = trim($_POST['emailProfesor']);
    $dni = trim($_POST['dniProfesor']);
    $telefono = trim($_POST['telefonoProfesor']);
    $direccion = trim($_POST['direccionProfesor']);
    $especialidad = trim($_POST['especialidad'] ?? '');
    $idEstado = $_POST['idEstado'] ?? 1;

    $errores = [];

    if (empty($nombre)) $errores['nombreProfesor'] = "El nombre es obligatorio";
    if (empty($email)) $errores['emailProfesor'] = "El email es obligatorio";
    if (empty($dni)) $errores['dniProfesor'] = "El DNI es obligatorio";
    if (empty($telefono)) $errores['telefonoProfesor'] = "El teléfono es obligatorio";
    if (empty($direccion)) $errores['direccionProfesor'] = "La dirección es obligatoria";
    if (empty($especialidad)) $errores['especialidad'] = "La especialidad es obligatoria";

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_profesor'] = $_POST;
        $url = ($accion == 'insertar') ? "agregarProfesores.php" : "modificarProfesores.php?id=" . $_POST['idProfesor'];
        header("Location: ../vistas/profesores/" . $url);
        exit;
    }

    $datos = [
        'nombreProfesor' => $nombre,
        'emailProfesor' => $email,
        'telefonoProfesor' => $telefono,
        'dniProfesor' => $dni,
        'especialidad' => $especialidad,
        'direccionProfesor' => $direccion,
        'idEstado' => $idEstado
    ];

    if ($accion == 'insertar') {
        $modelo->insertarProfesoresModelo($datos);
        $_SESSION['exito'] = "Profesor registrado";
    } else {
        $datos['idProfesor'] = $_POST['idProfesor'];
        $modelo->actualizarProfesoresModelo($datos);
        $_SESSION['exito'] = "Profesor actualizado";
    }

    header("Location: ../vistas/profesores/verProfesores.php");
    exit;
}

if (isset($_POST['accion']) && $_POST['accion'] == 'eliminar') {
    $id = $_POST['idProfesor'];
    $modelo->eliminarProfesoresModelo($id);
    $_SESSION['exito'] = "Profesor eliminado";
    header("Location: ../vistas/profesores/verProfesores.php");
    exit;
}
?>
