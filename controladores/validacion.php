<?php
session_start();
require_once __DIR__ . "/../modelos/directores.php";
require_once __DIR__ . "/../modelos/profesores.php";
require_once __DIR__ . "/../modelos/estudiantes.php";

if (isset($_POST["enviar"])) {
    $emailUsuarioRecibido = trim($_POST["usuario"]);
    $passwordUsuarioRecibida = trim($_POST["contrasena"]);

    if (empty($emailUsuarioRecibido) || empty($passwordUsuarioRecibida)) {
        $_SESSION["error"] = "Campos obligatorios.";
        header("Location: ../vistas/login.php");
        exit;
    }

    // Limpiamos cualquier variable de sesión previa para asegurar un inicio de sesión limpio
    unset($_SESSION['idAdmin'], $_SESSION['idProfesor'], $_SESSION['idEstudiante']);

    // Paso 1: Intentamos validar las credenciales en la tabla de Directores (Administradores)
    $datosAdmin = validarLoginDirector($emailUsuarioRecibido, $passwordUsuarioRecibida);
    if (!empty($datosAdmin)) {
        $_SESSION['idAdmin'] = $datosAdmin['idDirector'];
        header("Location: ../vistas/admin/inicio/dashboard.php");
        exit;
    }

    // Paso 2: Si no se encuentra en directores, verificamos en la tabla de Profesores
    $datosProfesor = validarLoginProfesor($emailUsuarioRecibido, $passwordUsuarioRecibida);
    if (!empty($datosProfesor)) {
        $_SESSION['idProfesor'] = $datosProfesor['idProfesor'];
        header("Location: ../vistas/profesores/inicio/dashboard.php");
        exit;
    }

    // Paso 3: Si no se encuentra en profesores, verificamos en la tabla de Estudiantes
    $datosEstudiante = validarLoginEstudiante($emailUsuarioRecibido, $passwordUsuarioRecibida);
    if (!empty($datosEstudiante)) {
        $_SESSION['idEstudiante'] = $datosEstudiante['idEstudiante'];
        header("Location: ../vistas/estudiantes/inicio/dashboard.php");
        exit;
    }

    // Paso 4: Si no se encuentra coincidencia en ninguna de las tablas, se considera un error de autenticación
    $_SESSION["error"] = "Datos incorrectos.";
    header("Location: ../vistas/login.php");
    exit;
}

header("Location: ../vistas/login.php");
exit;
?>
