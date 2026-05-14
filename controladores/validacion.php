<?php
/**
 * VALIDACIÓN DE LOGIN
 * Aquí es donde se mira quién intenta entrar.
 */

session_start();
require_once __DIR__ . "/../modelos/directores.php";
require_once __DIR__ . "/../modelos/profesores.php";
require_once __DIR__ . "/../modelos/estudiantes.php";

if (isset($_POST["enviar"])) {
    $email = strtolower(trim($_POST["usuario"]));
    $pass = trim($_POST["contrasena"]);

    $errores = [];

    // Chequeo de campos vacíos
    if (empty($email)) {
        $errores['usuario'] = "El correo electrónico es obligatorio.";
    }
    
    if (empty($pass)) {
        $errores['contrasena'] = "La contraseña es obligatoria.";
    }

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_login'] = $_POST;
        header("Location: ../vistas/login.php");
        exit;
    }

    // Limpiamos sesiones previas
    unset($_SESSION['idAdmin'], $_SESSION['idProfesor'], $_SESSION['idEstudiante']);

    // 1. ¿Es Admin?
    $admin = validarLoginDirector($email, $pass);
    if (!empty($admin)) {
        $_SESSION['idAdmin'] = $admin['idDirector'];
        header("Location: ../vistas/admin/inicio/dashboard.php");
        exit;
    }

    // 2. ¿Es Profesor?
    $profe = validarLoginProfesor($email, $pass);
    if (!empty($profe)) {
        $_SESSION['idProfesor'] = $profe['idProfesor'];
        header("Location: ../vistas/profesores/inicio/dashboard.php");
        exit;
    }

    // 3. ¿Es Estudiante?
    $estu = validarLoginEstudiante($email, $pass);
    if (!empty($estu)) {
        $_SESSION['idEstudiante'] = $estu['idEstudiante'];
        header("Location: ../vistas/estudiantes/inicio/dashboard.php");
        exit;
    }

    // Si nada coincide...
    $_SESSION['errores'] = ['usuario' => "El email o la contraseña no son correctos."];
    $_SESSION['datos_login'] = $_POST;
    header("Location: ../vistas/login.php");
    exit;
}

header("Location: ../vistas/login.php");
exit;
?>
