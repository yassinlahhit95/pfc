<?php
/* 
   CONTROLADOR DE VALIDACIÓN DE LOGIN
   Este archivo se encarga de mirar quién intenta entrar y mandarlo a su portal.
   Autor: Yassin Lahhit
*/

session_start();
require_once __DIR__ . "/../modelos/directores.php";
require_once __DIR__ . "/../modelos/profesores.php";
require_once __DIR__ . "/../modelos/estudiantes.php";

// Solo entramos aquí si le han dado al botón de enviar del formulario
if (isset($_POST["enviar"])) {
    $emailUsuarioRecibido = strtolower(trim($_POST["usuario"]));
    $passwordUsuarioRecibida = trim($_POST["contrasena"]);

    $errores = [];

    // Validamos que no nos dejen campos vacíos, que luego da error en la BD
    if (empty($emailUsuarioRecibido)) {
        $errores['usuario'] = "El correo electrónico es obligatorio.";
    }
    
    if (empty($passwordUsuarioRecibida)) {
        $errores['contrasena'] = "La contraseña es obligatoria.";
    }

    // Si hay fallos, volvemos atrás guardando lo que ha escrito para que no lo pierda
    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_login'] = $_POST;
        header("Location: ../vistas/login.php");
        exit;
    }

    // Limpiamos sesiones viejas por si acaso
    unset($_SESSION['idAdmin'], $_SESSION['idProfesor'], $_SESSION['idEstudiante']);

    // 1. Miramos si es Administrador (Director)
    $datosAdmin = validarLoginDirector($emailUsuarioRecibido, $passwordUsuarioRecibida);
    if (!empty($datosAdmin)) {
        $_SESSION['idAdmin'] = $datosAdmin['idDirector'];
        header("Location: ../vistas/admin/inicio/dashboard.php");
        exit;
    }

    // 2. Si no es admin, miramos si es Profesor
    $datosProfesor = validarLoginProfesor($emailUsuarioRecibido, $passwordUsuarioRecibida);
    if (!empty($datosProfesor)) {
        $_SESSION['idProfesor'] = $datosProfesor['idProfesor'];
        header("Location: ../vistas/profesores/inicio/dashboard.php");
        exit;
    }


    // 3. Y por último, probamos con los Estudiantes
    $datosEstudiante = validarLoginEstudiante($emailUsuarioRecibido, $passwordUsuarioRecibida);
    if (!empty($datosEstudiante)) {
        $_SESSION['idEstudiante'] = $datosEstudiante['idEstudiante'];
        header("Location: ../vistas/estudiantes/inicio/dashboard.php");
        exit;
    }

    // Si llegamos aquí es que nada ha coincidido... error de datos
    $_SESSION['errores'] = ['usuario' => "El email o la contraseña no son correctos."];
    $_SESSION['datos_login'] = $_POST;
    header("Location: ../vistas/login.php");
    exit;
}

// Si alguien intenta entrar a este archivo por la URL, lo mandamos al login
header("Location: ../vistas/login.php");
exit;
?>
