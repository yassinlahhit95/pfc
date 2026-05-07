<?php
session_start();

if (isset($_SESSION['idAdmin'])) {
    header("Location: admin/inicio/dashboard.php");
    exit;
} else if (isset($_SESSION['idProfesor'])) {
    header("Location: profesores/inicio/dashboard.php");
    exit;
} else if (isset($_SESSION['idEstudiante'])) {
    header("Location: estudiantes/inicio/dashboard.php");
    exit;
}

$mensajeError = '';
if (isset($_SESSION['error'])) {
    $mensajeError = $_SESSION['error'];
}
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión - Sistema de Gestión Escolar</title>
    <link rel="stylesheet" href="../public/css/admin.css">
    <link rel="stylesheet" href="../public/css/responsive.css">
    <link rel="icon" href="../public/imagenes/favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="login-body">

<div class="contenedor-login">
    <div class="tarjeta-login">
        <div class="logo-login"><i class="fas fa-school"></i></div>
        <h1 class="titulo-login">Portal Escolar</h1>
        <p class="subtitulo-login">Introduce tus credenciales para acceder</p>

        <?php if (!empty($mensajeError)) { ?>
        <div class="mensaje-error-login"><?php echo $mensajeError; ?></div>
        <?php } ?>

        <form action="../controladores/validacion.php" method="POST" class="formulario-login">
            <div class="campo-login">
                <label>Email:</label>
                <input type="text" name="usuario" placeholder="ejemplo@email.com" required>
            </div>

            <div class="campo-login">
                <label>Contraseña:</label>
                <input type="password" name="contrasena" placeholder="Tu contraseña" required>
            </div>

            <button type="submit" name="enviar" class="boton-enviar-login">Entrar al Sistema</button>
        </form>
    </div>
</div>

</body>
</html>
