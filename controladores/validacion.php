<?php
session_start();
require_once __DIR__ . "/../modelos/directores.php";
require_once __DIR__ . "/../modelos/profesores.php";
require_once __DIR__ . "/../modelos/estudiantes.php";

if (!isset($_POST["enviar"])) {
    header("Location: ../vistas/login.php");
    exit;
}

$email = strtolower(trim($_POST["usuario"]));
$pass  = trim($_POST["contrasena"]);

if (empty($email) || empty($pass)) {
    $_SESSION['errores'] = empty($email) && empty($pass)
        ? "El correo y la contraseña son obligatorios."
        : (empty($email) ? "El correo electrónico es obligatorio." : "La contraseña es obligatoria.");
    $_SESSION['datos_login'] = $_POST;
    header("Location: ../vistas/login.php");
    exit;
}

unset($_SESSION['idAdmin'], $_SESSION['idProfesor'], $_SESSION['idEstudiante']);

$admin = validarLoginDirector($email, $pass);
if ($admin) {
    $_SESSION['idAdmin'] = $admin['idDirector'];
    header("Location: ../vistas/admin/inicio/dashboard.php");
    exit;
}

$profe = validarLoginProfesor($email, $pass);
if ($profe) {
    $_SESSION['idProfesor'] = $profe['idProfesor'];
    header("Location: ../vistas/profesores/inicio/dashboard.php");
    exit;
}

$estu = validarLoginEstudiante($email, $pass);
if ($estu) {
    $_SESSION['idEstudiante'] = $estu['idEstudiante'];
    header("Location: ../vistas/estudiantes/inicio/dashboard.php");
    exit;
}

$_SESSION['errores'] = "El email o la contraseña no son correctos.";
$_SESSION['datos_login'] = $_POST;
header("Location: ../vistas/login.php");
exit;
?>
