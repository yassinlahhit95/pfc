<?php
session_start();
require_once "../modelos/conectar.php";

if (isset($_POST["enviar"])) {
    $emailUsuario = trim($_POST["usuario"]);
    $passwordUsuario = trim($_POST["contrasena"]);

    if (empty($emailUsuario)) {
        $_SESSION["error"] = "El campo de correo electrónico es obligatorio.";
        header("Location: /pfc/index.php");
        exit;
    }

    if (empty($passwordUsuario)) {
        $_SESSION["error"] = "El campo de contraseña es obligatorio.";
        header("Location: /pfc/index.php");
        exit;
    }

    $conexion = obtenerConexion();

    // Limpiamos cualquier sesión anterior para empezar de cero
    unset($_SESSION['idAdmin']);
    unset($_SESSION['idProfesor']);
    unset($_SESSION['idEstudiante']);

    // 1. Intentamos buscar en DIRECTORES (Administradores)
    $sqlAdmin = "SELECT * FROM directores WHERE emailDirector = '$emailUsuario' AND password = '$passwordUsuario'";
    $resultadoAdmin = mysqli_query($conexion, $sqlAdmin);
    $datosAdmin = mysqli_fetch_assoc($resultadoAdmin);

    if (!empty($datosAdmin)) {
        $_SESSION['idAdmin'] = $datosAdmin['idDirector'];
        mysqli_close($conexion);
        header("Location: /pfc/vistas/admin/dashboard.php");
        exit;
    }

    // 2. Si no es admin, buscamos en PROFESORES
    $sqlProfesor = "SELECT * FROM profesores WHERE emailProfesor = '$emailUsuario' AND password = '$passwordUsuario'";
    $resultadoProfesor = mysqli_query($conexion, $sqlProfesor);
    $datosProfesor = mysqli_fetch_assoc($resultadoProfesor);

    if (!empty($datosProfesor)) {
        $_SESSION['idProfesor'] = $datosProfesor['idProfesor'];
        mysqli_close($conexion);
        header("Location: /pfc/vistas/profesores/dashboard.php");
        exit;
    }

    // 3. Si no es profesor, buscamos en ESTUDIANTES
    $sqlEstudiante = "SELECT * FROM estudiantes WHERE emailEstudiante = '$emailUsuario' AND password = '$passwordUsuario'";
    $resultadoEstudiante = mysqli_query($conexion, $sqlEstudiante);
    $datosEstudiante = mysqli_fetch_assoc($resultadoEstudiante);

    if (!empty($datosEstudiante)) {
        $_SESSION['idEstudiante'] = $datosEstudiante['idEstudiante'];
        mysqli_close($conexion);
        header("Location: /pfc/vistas/estudiantes/dashboard.php");
        exit;
    }

    // 4. Si llegamos aquí, es que no existe en ninguna tabla con esos datos
    mysqli_close($conexion);
    $_SESSION["error"] = "Los datos introducidos no son correctos o el usuario no existe.";
    header("Location: /pfc/index.php");
    exit;
}

header("Location: /pfc/index.php");
exit;
?>
