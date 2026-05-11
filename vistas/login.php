<?php
/* 
   PROYECTO: AulaPro - Sistema de Gestión Escolar
   AUTOR: Yassin Lahhit (CPS Ibaiondo)
   FECHA: Mayo 2024
   NOTA: Esta es la página principal de entrada para todos los usuarios.
*/

session_start();

// Si ya hay sesión, mandamos al usuario a su sitio sin que tenga que loguearse otra vez
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


$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_login'] ?? [];
unset($_SESSION['errores'], $_SESSION['datos_login']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AULAPRO | INICIAR SESIÓN</title>
    <link rel="shortcut icon" href="../public/imagenes/favicon.ico" type="image/x-icon">
    <link rel="icon" href="../public/imagenes/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../public/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>
<body>
    <div class="container">
        <div class="login-section">
            <div class="logo">
                                <div class="logo-text">Aula</div>

                <div class="logo-icon">PRO</div>
            </div>

            <h1 class="welcome-title">Acceso al Sistema</h1>
            <p class="welcome-subtitle">Gestión académica integral para tu centro educativo.</p>

            <form action="../controladores/validacion.php" method="POST">
                <div class="form-group">
                    <label class="form-label">Correo Electrónico</label>
                    <input 
                        type="text" 
                        name="usuario" 
                        class="form-input" 
                        placeholder="nombre@aulapro.com"
                        value="<?php echo htmlspecialchars($datos['usuario'] ?? $_GET['u'] ?? ''); ?>"
                    id="campo-usuario"
                    >
                    <?php if (!empty($errores['usuario'])) { ?>
                        <span class="error-campo"><?php echo $errores['usuario']; ?></span>
                    <?php } ?>
                </div>

                <div class="form-group">
                    <label class="form-label">Contraseña</label>
                    <input 
                        type="password" 
                        name="contrasena" 
                        class="form-input" 
                        placeholder="••••••••"
                    >
                    <?php if (!empty($errores['contrasena'])) { ?>
                        <span class="error-campo"><?php echo $errores['contrasena']; ?></span>
                    <?php } ?>
                </div>

                <button type="submit" name="enviar" class="login-button">INICIAR SESIÓN</button>
            </form>
        </div>

        <div class="promo-section">
            <div class="particles">
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
            </div>

            <div class="promo-content">
                <h2 class="promo-title">Plataforma Educativa Tri-Portal</h2>
                <p class="promo-subtitle">Conecta estudiantes, profesores y administración en un entorno unificado y eficiente.</p>
                
                <div class="dashboard-preview">
                    <img src="../public/imagenes/aulapro.png" alt="AulaPro">
                </div>
            </div>

            <div class="footer-info">
                <span>© <?= date('Y') ?> AulaPro - TFG</span>
                <span>v1.0</span>
            </div>
        </div>
    </div>
    <script>
      // Si el usuario viene de la landing y ya tenemos su correo, ponemos el foco en la contraseña
      <?php if (!empty($_GET['u'])) { ?>
      document.addEventListener('DOMContentLoaded', () => {
        const pwd = document.querySelector('input[name="contrasena"]');
        if (pwd) pwd.focus();
      });
      <?php } ?>
    </script>
</body>
</html>