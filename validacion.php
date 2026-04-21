<?php
session_start();
require_once "modelos/conectar.php";

if (!isset($_POST["enviar"])) {
    $_SESSION["error"] = "El formulario no está bien definido.";
    header("Location: index.php");
    exit;
}

$email = strip_tags(trim($_POST["usuario"]));
$password = strip_tags(trim($_POST["contrasena"]));
$tipoUsuario = $_POST['tipoUsuario'];

if (empty($email)) {
    $_SESSION["error"] = "Email vacío.";
    header("Location: index.php");
    exit;
}

if (empty($password)) {
    $_SESSION["error"] = "Contraseña vacía.";
    header("Location: index.php");
    exit;
}

$conexion = obtenerConexion();

if ($tipoUsuario == 'admin') {
    $sql = "SELECT idDirector, nombreDirector FROM directores WHERE emailDirector = '$email' AND password = '$password'";
    $res = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($res);
    
    if ($fila) {
        $_SESSION["tipoUsuario"] = "admin";
        $_SESSION["idAdmin"] = $fila['idDirector'];
        $_SESSION["nombreUsuario"] = $fila['nombreDirector'];
        header("Location: /pfc/vistas/admin/dashboard.php");
        exit;
    }
} else if ($tipoUsuario == 'profesor') {
    $sql = "SELECT idProfesor, nombreProfesor FROM profesores WHERE emailProfesor = '$email' AND password = '$password'";
    $res = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($res);
    
    if ($fila) {
        $_SESSION["tipoUsuario"] = "profesor";
        $_SESSION["idProfesor"] = $fila['idProfesor'];
        $_SESSION["nombreUsuario"] = $fila['nombreProfesor'];
        header("Location: /pfc/vistas/profesores/dashboard.php");
        exit;
    }
} else if ($tipoUsuario == 'estudiante') {
    $sql = "SELECT idEstudiante, nombreEstudiante FROM estudiantes WHERE emailEstudiante = '$email' AND password = '$password'";
    $res = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($res);
    
    if ($fila) {
        $_SESSION["tipoUsuario"] = "estudiante";
        $_SESSION["idEstudiante"] = $fila['idEstudiante'];
        $_SESSION["nombreUsuario"] = $fila['nombreEstudiante'];
        header("Location: /pfc/vistas/estudiantes/dashboard.php");
        exit;
    }
}

mysqli_close($conexion);

$_SESSION["error"] = "Los datos no son correctos o el tipo de usuario no coincide.";
header("Location: index.php");
exit;
?>