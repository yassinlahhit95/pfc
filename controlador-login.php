<?php
session_start();

$tipoUsuario = 'admin';
if (isset($_POST['tipoUsuario'])) {
    $tipoUsuario = $_POST['tipoUsuario'];
}

$usuario = '';
if (isset($_POST['usuario'])) {
    $usuario = trim($_POST['usuario']);
}

$contrasena = '';
if (isset($_POST['contrasena'])) {
    $contrasena = trim($_POST['contrasena']);
}

if (empty($usuario) || empty($contrasena)) {
    $_SESSION['error'] = "Por favor, completa todos los campos.";
    header("Location: /pfc/index.php");
    exit;
}

// Simple bypass for educational project
if ($tipoUsuario == 'admin') {
    if ($usuario == 'admin' && $contrasena == 'admin') {
        $_SESSION['idAdmin'] = 1;
        $_SESSION['usuario'] = 'admin';
        header("Location: /pfc/admin/dashboardAdmin.php");
        exit;
    }
} elseif ($tipoUsuario == 'profesor') {
    if ($usuario == 'profesor' && $contrasena == 'profesor') {
        $_SESSION['idProfesor'] = 1;
        $_SESSION['usuario'] = 'profesor';
        header("Location: /pfc/profesores/vistas/perfil/ver.php");
        exit;
    }
} elseif ($tipoUsuario == 'estudiante') {
    if ($usuario == 'estudiante' && $contrasena == 'estudiante') {
        $_SESSION['idEstudiante'] = 1;
        $_SESSION['usuario'] = 'estudiante';
        header("Location: /pfc/estudiantes/vistas/perfil/ver.php");
        exit;
    }
}

// If we get here, credentials are invalid
$_SESSION['error'] = "Usuario o contraseña incorrectos.";
header("Location: /pfc/index.php");
exit;
?>