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

    // Limpiamos cualquier sesión anterior para empezar de cero
    unset($_SESSION['idAdmin'], $_SESSION['idProfesor'], $_SESSION['idEstudiante']);

    // 1. Intentamos buscar en DIRECTORES (Administradores)
    $datosAdmin = validarLoginDirector($emailUsuarioRecibido, $passwordUsuarioRecibida);
    if (!empty($datosAdmin)) {
        $_SESSION['idAdmin'] = $datosAdmin['idDirector'];
        header("Location: ../vistas/admin/inicio/dashboard.php");
        exit;
    }

    // 2. Si no es admin, buscamos en PROFESORES
    $datosProfesor = validarLoginProfesor($emailUsuarioRecibido, $passwordUsuarioRecibida);
    if (!empty($datosProfesor)) {
        $_SESSION['idProfesor'] = $datosProfesor['idProfesor'];
        header("Location: ../vistas/profesores/inicio/dashboard.php");
        exit;
    }

    // 3. Si no es profesor, buscamos en ESTUDIANTES
    $datosEstudiante = validarLoginEstudiante($emailUsuarioRecibido, $passwordUsuarioRecibida);
    if (!empty($datosEstudiante)) {
        $_SESSION['idEstudiante'] = $datosEstudiante['idEstudiante'];
        header("Location: ../vistas/estudiantes/inicio/dashboard.php");
        exit;
    }

    // 4. Si llegamos aquí, es que no existe en ninguna tabla con esos datos
    $_SESSION["error"] = "Datos incorrectos.";
    header("Location: ../vistas/login.php");
    exit;
}

header("Location: ../vistas/login.php");
exit;
