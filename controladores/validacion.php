<?php
session_start();
require_once __DIR__ . "/../modelos/directores.php";
require_once __DIR__ . "/../modelos/profesores.php";
require_once __DIR__ . "/../modelos/estudiantes.php";

if (isset($_POST["enviar"])) {
    $emailUsuarioRecibido = trim($_POST["usuario"]);
    $passwordUsuarioRecibida = trim($_POST["contrasena"]);

    $errores = [];
    if (empty($emailUsuarioRecibido)) {
        $errores['usuario'] = "El email es obligatorio.";
    }
    if (empty($passwordUsuarioRecibida)) {
        $errores['contrasena'] = "La contraseña es obligatoria.";
    }

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_login'] = $_POST;
        header("Location: ../vistas/login.php");
        exit;
    }

    unset($_SESSION['idAdmin'], $_SESSION['idProfesor'], $_SESSION['idEstudiante']);

    $datosAdmin = validarLoginDirector($emailUsuarioRecibido, $passwordUsuarioRecibida);
    if (!empty($datosAdmin)) {
        $_SESSION['idAdmin'] = $datosAdmin['idDirector'];
        header("Location: ../vistas/admin/inicio/dashboard.php");
        exit;
    }

    $datosProfesor = validarLoginProfesor($emailUsuarioRecibido, $passwordUsuarioRecibida);
    if (!empty($datosProfesor)) {
        $_SESSION['idProfesor'] = $datosProfesor['idProfesor'];
        header("Location: ../vistas/profesores/inicio/dashboard.php");
        exit;
    }

    $datosEstudiante = validarLoginEstudiante($emailUsuarioRecibido, $passwordUsuarioRecibida);
    if (!empty($datosEstudiante)) {
        $_SESSION['idEstudiante'] = $datosEstudiante['idEstudiante'];
        header("Location: ../vistas/estudiantes/inicio/dashboard.php");
        exit;
    }

    $_SESSION['errores'] = ['usuario' => "Email o contraseña incorrectos."];
    $_SESSION['datos_login'] = $_POST;
    header("Location: ../vistas/login.php");
    exit;
}

header("Location: ../vistas/login.php");
exit;
?>
