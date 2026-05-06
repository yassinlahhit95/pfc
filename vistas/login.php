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
    <link rel="icon" href="../public/imagenes/favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .contenedor-login { width: 100%; max-width: 400px; padding: 20px; }
        .tarjeta-login { background: white; border-radius: 10px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); padding: 40px; text-align: center; }
        .logo-login { font-size: 48px; margin-bottom: 20px; color: #667eea; }
        .titulo-login { font-size: 24px; font-weight: bold; color: #333; margin-bottom: 10px; }
        .subtitulo-login { color: #999; margin-bottom: 30px; font-size: 14px; }
        .formulario-login { display: flex; flex-direction: column; gap: 15px; }
        .campo-login { text-align: left; }
        .campo-login label { display: block; margin-bottom: 5px; color: #333; font-weight: 500; font-size: 14px; }
        .campo-login input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; }
        .boton-enviar-login { background: #667eea; color: white; padding: 12px; border: none; border-radius: 5px; font-size: 16px; font-weight: bold; cursor: pointer; margin-top: 10px; }
        .mensaje-error-login { background: #fee; color: #c33; padding: 12px; border-radius: 5px; margin-bottom: 20px; font-size: 14px; border-left: 4px solid #c33; }
    </style>
</head>
<body>

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
                <input type="text" name="usuario" placeholder="ejemplo@email.com">
            </div>

            <div class="campo-login">
                <label>Contraseña:</label>
                <input type="password" name="contrasena" placeholder="Tu contraseña">
            </div>

            <button type="submit" name="enviar" class="boton-enviar-login">Entrar al Sistema</button>
        </form>
    </div>
</div>

</body>
</html>
