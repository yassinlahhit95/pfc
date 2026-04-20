<?php
session_start();

if(!isset($_POST["enviar"])){
    $_SESSION["error"] = "El formulario no está bien definido.";
    header("Location:index.php");
    exit;
}

if(!isset($_POST["usuario"])){
    $_SESSION["error"] = "El formulario no está bien definido debido al nombre.";
    header("Location:index.php");
    exit;
}

$_SESSION["usuario"] = strip_tags(trim($_POST["usuario"]));
if(empty($_SESSION["usuario"])){
    $_SESSION["error"] = "Nombre de usuario vacío.";
    header("Location: index.php");
    exit;
}

if(is_numeric($_SESSION["usuario"])){
    $_SESSION["error"] = "Ha introducido un valor numérico en el nombre.";
    header("Location: index.php");
    exit;
}

if(!isset($_POST["contrasena"])){
    $_SESSION["error"] = "Formulario mal definido por la contraseña.";
    header("Location: index.php");
    exit;
}

$_SESSION["psw"] = strip_tags(trim($_POST["contrasena"]));
if(empty($_SESSION["psw"])){
    $_SESSION["error"] = "Contraseña vacía";
    header("Location: index.php");
    exit;
}

$tipoUsuario = 'admin';
if (isset($_POST['tipoUsuario'])) {
    $tipoUsuario = $_POST['tipoUsuario'];
}

// Validación simple para el proyecto de ejercicio
if ($tipoUsuario == 'admin') {
    if (($_SESSION["usuario"] == "admin") && ($_SESSION["psw"] == "admin")) {
        $_SESSION["tipoUsuario"] = "admin";
        $_SESSION["idAdmin"] = 1;
        header("Location:admin/dashboardAdmin.php");
        exit;
    }
} elseif ($tipoUsuario == 'profesor') {
    if (($_SESSION["usuario"] == "profesor") && ($_SESSION["psw"] == "profesor")) {
        $_SESSION["tipoUsuario"] = "profesor";
        $_SESSION["idProfesor"] = 1;
        header("Location:profesores/vistas/perfil/ver.php");
        exit;
    }
} elseif ($tipoUsuario == 'estudiante') {
    if (($_SESSION["usuario"] == "estudiante") && ($_SESSION["psw"] == "estudiante")) {
        $_SESSION["tipoUsuario"] = "estudiante";
        $_SESSION["idEstudiante"] = 1;
        header("Location:estudiantes/vistas/perfil/ver.php");
        exit;
    }
}

$_SESSION["error"] = "Los datos no están registrados en el sistema";
header("Location: index.php");
exit;
?>