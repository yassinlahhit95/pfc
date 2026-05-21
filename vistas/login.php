<?php

session_start();

if (isset($_SESSION['idAdmin']))      { header("Location: admin/inicio/dashboard.php");      exit; }
if (isset($_SESSION['idProfesor']))   { header("Location: profesores/inicio/dashboard.php");  exit; }
if (isset($_SESSION['idEstudiante'])) { header("Location: estudiantes/inicio/dashboard.php"); exit; }

$errores = $_SESSION['errores'] ?? null;
$datos = $_SESSION['datos_login'] ?? [];
unset($_SESSION['errores'], $_SESSION['datos_login']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso — AulaPro</title>
    <link rel="icon" href="/public/imagenes/favicon.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../public/css/login.css">
</head>
<body>

<div class="login-page">

    <!-- PANEL IZQUIERDO -->
    <div class="panel-izquierdo">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>

        <div class="panel-contenido">
            <div class="panel-logo">
                <img src="../public/imagenes/aulapro.png" alt="AulaPro">
            </div>
            <h1 class="panel-titulo">Gestión académica <span>sin complicaciones</span></h1>
            <p class="panel-desc">Alumnos, profesores, calificaciones y pagos en un solo lugar. Todo lo que tu centro necesita, ordenado y siempre accesible.</p>
            <div class="panel-stats">
                <div class="stat"><strong>3</strong><span>Roles de acceso</span></div>
                <div class="stat"><strong>100%</strong><span>En la nube</span></div>
                <div class="stat"><strong>0</strong><span>Instalaciones</span></div>
            </div>
        </div>
    </div>

    <!-- PANEL DERECHO -->
    <div class="panel-derecho">
        <div class="form-contenedor">

            <div class="form-cabecera">
                <h2>Bienvenido</h2>
                <p>Accede a tu cuenta para continuar</p>
            </div>

            <?php if ($errores) { ?>
            <div class="error-alerta">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <?= $errores ?>
            </div>
            <?php } ?>

            <form action="../controladores/validacion.php" method="POST" id="loginForm">

                <div class="campo-grupo">
                    <label for="usuario">Usuario o Email</label>
                    <input type="text" id="usuario" name="usuario" placeholder="tu@email.com" value="<?= $datos['usuario'] ?? $_GET['u'] ?? '' ?>" autofocus>
                </div>

                <div class="campo-grupo">
                    <label for="contrasena">Contraseña</label>
                    <div class="campo-password">
                        <input type="password" id="contrasena" name="contrasena" placeholder="••••••••">
                        <button type="button" id="togglePassword" class="ojo-boton" aria-label="Mostrar/ocultar contraseña">
                            <svg id="ojo-svg" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" name="enviar" class="boton-acceso">Iniciar sesión</button>

                <a href="../index.html" class="enlace-volver">← Volver al inicio</a>

            </form>
        </div>

        <p class="form-pie">&copy; 2025/2026 AulaPro</p>
    </div>

</div>

<script>
$(function() {
    $('#togglePassword').on('click', function() {
        var $pwd = $('#contrasena');
        var visible = $pwd.attr('type') === 'text';
        $pwd.attr('type', visible ? 'password' : 'text');
        $('#ojo-svg').html(visible
            ? '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>'
            : '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>');
    });

    <?php if (!empty($_GET['u'])) { ?>
    $('#contrasena').focus();
    <?php } ?>
});
</script>

</body>
</html>
