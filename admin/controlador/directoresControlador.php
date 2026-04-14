<?php
session_start();
require_once "../modelos/conexion.php";
require_once "../modelos/directores.php";

$con = new Conexion();
$conexion = $con->conectar();
$modelo = new director($conexion);

if (isset($_POST['guardarDirector'])) {
    $accion = $_POST['accion'];
    
    unset($_SESSION['errores']);
    unset($_SESSION['datos_director']);

    $nombre = trim($_POST['nombreDirector']);
    $email = trim($_POST['emailDirector']);
    $dni = trim($_POST['dniDirector']);
    $telefono = trim($_POST['telefonoDirector']);
    $direccion = trim($_POST['direccionDirector']);
    $ciudad = trim($_POST['ciudadDirector']);
    $cp = trim($_POST['codigoPostalDirector']);
    $fechaAlta = $_POST['fechaAltaDirector'];
    $idEstado = $_POST['idEstado'] ?? 1;

    $errores = [];

    if (empty($nombre)) $errores['nombreDirector'] = "El nombre es obligatorio";
    if (empty($email)) $errores['emailDirector'] = "El email es obligatorio";
    if (empty($dni)) $errores['dniDirector'] = "El DNI es obligatorio";
    if (empty($telefono)) $errores['telefonoDirector'] = "El teléfono es obligatorio";
    if (empty($direccion)) $errores['direccionDirector'] = "La dirección es obligatoria";
    if (empty($ciudad)) $errores['ciudadDirector'] = "La ciudad es obligatoria";
    if (empty($cp)) $errores['codigoPostalDirector'] = "El código postal es obligatorio";
    if (empty($fechaAlta)) $errores['fechaAltaDirector'] = "La fecha de alta es obligatoria";

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_director'] = $_POST;
        $url = ($accion == 'insertar') ? "agregarDirectores.php" : "modificarDirectores.php?id=" . $_POST['idDirector'];
        header("Location: ../vistas/directores/" . $url);
        exit;
    }

    $datos = [
        'nombreDirector' => $nombre,
        'emailDirector' => $email,
        'dniDirector' => $dni,
        'telefonoDirector' => $telefono,
        'direccionDirector' => $direccion,
        'ciudadDirector' => $ciudad,
        'codigoPostalDirector' => $cp,
        'fechaAltaDirector' => $fechaAlta,
        'idEstado' => $idEstado
    ];

    if ($accion == 'insertar') {
        $modelo->insertarDirectoresModelo($datos);
        $_SESSION['exito'] = "Director registrado";
    } else {
        $datos['idDirector'] = $_POST['idDirector'];
        $modelo->actualizarDirectoresModelo($datos);
        $_SESSION['exito'] = "Director actualizado";
    }

    header("Location: ../vistas/directores/verDirectores.php");
    exit;
}

if (isset($_POST['accion']) && $_POST['accion'] == 'eliminar') {
    $id = $_POST['idDirector'];
    $modelo->eliminarDirectoresModelo($id);
    $_SESSION['exito'] = "Director borrado";
    header("Location: ../vistas/directores/verDirectores.php");
    exit;
}
?>
