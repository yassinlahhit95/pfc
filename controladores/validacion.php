<?php
session_start();
require_once __DIR__ . "/../modelos/directores.php";
require_once __DIR__ . "/../modelos/profesores.php";
require_once __DIR__ . "/../modelos/estudiantes.php";

// Si le dan al botón de entrar...
if (isset($_POST["enviar"])) {
    $email = strtolower(trim($_POST["usuario"]));
    $pass = trim($_POST["contrasena"]);

    $errores = [];

    // Validaciones básicas de que no venga vacío
    if (empty($email)) {
        $errores['usuario'] = "El correo electrónico es obligatorio.";
    }
    
    if (empty($pass)) {
        $errores['contrasena'] = "La contraseña es obligatoria.";
    }

    // Si hay fallos, de vuelta al login con los errores
    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_login'] = $_POST;
        header("Location: ../vistas/login.php");
        exit;
    }

    // Limpiamos sesiones viejas por si acaso
    unset($_SESSION['idAdmin'], $_SESSION['idProfesor'], $_SESSION['idEstudiante']);

    // LOGICA DE LOGIN:
    // 1. Miramos si es un Director/Admin
    $admin = validarLoginDirector($email, $pass);
    if (!empty($admin)) {
        $_SESSION['idAdmin'] = $admin['idDirector'];
        header("Location: ../vistas/admin/inicio/dashboard.php");
        exit;
    }

    // 2. Si no es admin, probamos con Profesores
    $profe = validarLoginProfesor($email, $pass);
    if (!empty($profe)) {
        $_SESSION['idProfesor'] = $profe['idProfesor'];
        header("Location: ../vistas/profesores/inicio/dashboard.php");
        exit;
    }

    // 3. Y por último, Estudiantes
    $estu = validarLoginEstudiante($email, $pass);
    if (!empty($estu)) {
        $_SESSION['idEstudiante'] = $estu['idEstudiante'];
        header("Location: ../vistas/estudiantes/inicio/dashboard.php");
        exit;
    }

    $_SESSION['errores'] = ['usuario' => "El email o la contraseña no son correctos."];
    $_SESSION['datos_login'] = $_POST;
    header("Location: ../vistas/login.php");
    exit;
}

header("Location: ../vistas/login.php");
exit;
?>
