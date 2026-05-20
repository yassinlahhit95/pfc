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
    <title>Acceso al Sistema - AulaPro</title>
    <link rel="icon" href="/public/imagenes/favicon.ico" type="image/x-icon">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Outfit:wght@300;400;600;700;900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../public/css/login.css">
</head>
<body>

<main class="login-wrapper">
    <div class="left">
        <div class="logo">PORTAL</div><br>
        <div class="title-wrapper">
            <h1 class="modern-title">
                AulaPro
            </h1>
        </div>
        <div class="subtitle">
            Plataforma Integral de Gestión Académica: Conectando Estudiantes, Profesores y Administración para una Excelencia Educativa Superior.
        </div>
    </div>

    <div class="login-card">
        <?php if ($errores) { ?>
            <div id="errorMessage" class="error-message">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <?= $errores ?>
            </div>
        <?php } ?>

        <form action="../controladores/validacion.php" method="POST" id="loginForm">
            <label for="usuario">Usuario / Email</label>
            <input type="text" id="usuario" name="usuario" placeholder="nombre@aulapro.com" value="<?= $datos['usuario'] ?? $_GET['u'] ?? '' ?>" autofocus>
            
            <label for="contrasena">Contraseña</label>
            <div class="password-wrapper">
                <input type="password" id="contrasena" name="contrasena" placeholder="••••••••">
                <button type="button" class="toggle-password" id="togglePassword">
                    <span class="eye-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </span>
                </button>
            </div>

            <button type="submit" name="enviar" class="btn-primary">Iniciar Sesión</button>

            <div class="divider"><span>o</span></div>

            <div class="btn-group">
                <a href="../index.html" class="btn-secondary" style="text-decoration: none; text-align: center; display: flex; align-items: center; justify-content: center;">Volver al Inicio</a>
            </div>
        </form>
    </div>
</main>

<script>
    var togglePassword = document.getElementById("togglePassword");
    var passwordField = document.getElementById("contrasena");

    if (togglePassword && passwordField) {
        togglePassword.addEventListener("click", function(e) {
            e.preventDefault();
            var isPassword = passwordField.type === "password";
            passwordField.type = isPassword ? "text" : "password";

            var svg = togglePassword.querySelector('svg');
            if (isPassword) {
                svg.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
            } else {
                svg.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
            }
        });
    }

    <?php if (!empty($_GET['u'])) { ?>
    document.addEventListener('DOMContentLoaded', function() {
        var pwd = document.getElementById('contrasena');
        if (pwd) pwd.focus();
    });
    <?php } ?>
</script>

</body>
</html>
