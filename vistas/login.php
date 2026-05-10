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
    <link rel="icon" href="../public/imagenes/favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #ffffff;
            height: 100vh;
            overflow: hidden;
        }

        .container {
            display: flex;
            width: 100%;
            height: 100vh;
        }

        .login-section {
            width: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px 80px;
            background-color: #ffffff;
        }

        .logo {
            display: flex;
            align-items: center;
            margin-bottom: 40px;
            gap: 12px;
            animation: slideInLeft 0.8s ease-out;
        }

        .logo-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 24px;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
            transition: transform 0.3s ease;
        }

        .logo-icon:hover {
            transform: rotate(10deg) scale(1.1);
        }

        .logo-text {
            font-size: 28px;
            font-weight: 800;
            color: #111827;
            letter-spacing: -0.5px;
        }

        .welcome-title {
            font-size: 36px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 10px;
            animation: fadeInUp 0.8s ease-out 0.2s both;
        }

        .welcome-subtitle {
            color: #6b7280;
            margin-bottom: 50px;
            font-size: 16px;
            font-weight: 500;
            animation: fadeInUp 0.8s ease-out 0.3s both;
        }

        .form-group {
            margin-bottom: 24px;
            animation: fadeInUp 0.8s ease-out both;
        }

        .form-group:nth-child(1) { animation-delay: 0.4s; }
        .form-group:nth-child(2) { animation-delay: 0.5s; }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #374151;
            font-weight: 600;
            font-size: 14px;
        }

        .form-input {
            width: 100%;
            padding: 16px;
            border: 2px solid #667EEA;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f9fafb;
        }

        .form-input:focus {
            border-color: #4f46e5;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
            outline: none;
            transform: translateY(-2px);
        }

        .error-campo {
            color: #f87171;
            font-size: 13px;
            font-weight: 600;
            margin-top: 5px;
            display: block;
        }

        .login-button {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            animation: fadeInUp 0.8s ease-out 0.6s both;
            position: relative;
            overflow: hidden;
        }

        .login-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s ease;
        }

        .login-button:hover::before {
            left: 100%;
        }

        .login-button:hover {
            background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(79, 70, 229, 0.4);
        }

        .promo-section {
            width: 50%;
            background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px 80px;
            position: relative;
            overflow: hidden;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            width: 10px;
            height: 10px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            animation: particleFloat 15s infinite ease-in-out;
        }

        .particle:nth-child(1) { left: 10%; top: 20%; }
        .particle:nth-child(2) { left: 30%; top: 60%; }
        .particle:nth-child(3) { left: 50%; top: 40%; }
        .particle:nth-child(4) { left: 70%; top: 80%; }
        .particle:nth-child(5) { left: 90%; top: 30%; }

        @keyframes particleFloat {
            0%, 100% { transform: translateY(0) translateX(0) scale(1); opacity: 0.3; }
            50% { transform: translateY(-40px) translateX(-10px) scale(0.8); opacity: 0.4; }
        }

        .promo-content { position: relative; z-index: 1; }
        .promo-title {
            font-size: 48px;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 20px;
            line-height: 1.1;
            text-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .promo-subtitle {
            font-size: 20px;
            color: rgba(255, 255, 255, 0.95);
            margin-bottom: 40px;
            line-height: 1.6;
        }

        .dashboard-preview {
            width: 100%;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.3);
            border: 3px solid rgba(255, 255, 255, 0.2);
            background: #000;
        }

        .dashboard-preview video {
            width: 100%;
            height: auto;
            display: block;
        }

        .footer-info {
            position: absolute;
            bottom: 40px;
            left: 80px;
            right: 80px;
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            display: flex;
            justify-content: space-between;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-50px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @media (max-width: 968px) {
            .container { flex-direction: column; height: auto; min-height: 100vh; }
            .login-section { width: 100%; padding: 40px 30px; }
            .promo-section { width: 100%; padding: 40px 30px; min-height: 500px; display: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-section">
            <div class="logo">
                <div class="logo-icon">A</div>
                <div class="logo-text">AulaPro</div>
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
                        required
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
                        required
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
                    <video autoplay muted loop playsinline>
                        <source src="../public/videos/intro.mp4" type="video/mp4">
                        Tu navegador no soporta el elemento de video.
                    </video>
                </div>
            </div>

            <div class="footer-info">
                <span>Copyright © <?= date('Y') ?> AulaPro - TFG</span>
                <span>v1.0</span>
            </div>
        </div>
    </div>
    <script>
      <?php if (!empty($_GET['u'])) { ?>
      document.addEventListener('DOMContentLoaded', () => {
        const pwd = document.querySelector('input[name="contrasena"]');
        if (pwd) pwd.focus();
      });
      <?php } ?>
    </script>
</body>
</html>