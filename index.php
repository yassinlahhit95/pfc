<?php
session_start();

// Si ya estamos autenticados, redirigir a la sección correspondiente
if (isset($_SESSION['idAdmin'])) {
    header("Location: /pfc/admin/dashboardAdmin.php");
    exit;
} elseif (isset($_SESSION['idProfesor'])) {
    header("Location: /pfc/profesores/vistas/perfil/ver.php");
    exit;
} elseif (isset($_SESSION['idEstudiante'])) {
    header("Location: /pfc/estudiantes/vistas/perfil/ver.php");
    exit;
}

// Mostrar login
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
    <link rel="stylesheet" href="/pfc/admin/estiloAdmin/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .contenedor-login {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }
        
        .tarjeta-login {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 40px;
            text-align: center;
        }
        
        .logo-login {
            font-size: 48px;
            margin-bottom: 20px;
            color: #667eea;
        }
        
        .titulo-login {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        
        .subtitulo-login {
            color: #999;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .formulario-login {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .campo-login {
            text-align: left;
        }
        
        .campo-login label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }
        
        .campo-login input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .campo-login input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
        }
        
        .selector-rol {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .boton-rol {
            flex: 1;
            padding: 10px;
            border: 2px solid #ddd;
            background: white;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 12px;
            font-weight: 500;
        }
        
        .boton-rol:hover {
            border-color: #667eea;
        }
        
        .boton-rol.activo {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        
        .boton-enviar-login {
            background: #667eea;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .boton-enviar-login:hover {
            background: #764ba2;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .mensaje-error-login {
            background: #fee;
            color: #c33;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
            border-left: 4px solid #c33;
        }
    </style>
</head>
<body>

<div class="contenedor-login">
    <div class="tarjeta-login">
        <div class="logo-login">
            <i class="fas fa-school"></i>
        </div>
        
        <h1 class="titulo-login">Portal Escolar</h1>
        <p class="subtitulo-login">Sistema de Gestión</p>
        
        <?php if ($mensajeError) { ?>
        <div class="mensaje-error-login">
            <i class="fas fa-exclamation-circle"></i> <?php echo $mensajeError; ?>
        </div>
        <?php } ?>
        
        <form action="/pfc/validacion.php" method="POST" class="formulario-login">
            <div>
                <label style="display: block; margin-bottom: 10px; color: #333; font-weight: 500;">Selecciona tu tipo de usuario:</label>
                <div class="selector-rol">
                    <button type="button" class="boton-rol activo" data-rol="admin">
                        <i class="fas fa-user-tie"></i> Admin
                    </button>
                    <button type="button" class="boton-rol" data-rol="profesor">
                        <i class="fas fa-chalkboard-user"></i> Profesor
                    </button>
                    <button type="button" class="boton-rol" data-rol="estudiante">
                        <i class="fas fa-user-graduate"></i> Estudiante
                    </button>
                </div>
            </div>
            
            <input type="hidden" name="tipoUsuario" id="tipoUsuario" value="admin">
            
            <div class="campo-login">
                <label>Email o Usuario:</label>
                <input type="text" name="usuario" placeholder="Tu email o usuario" required>
            </div>
            
            <div class="campo-login">
                <label>Contraseña:</label>
                <input type="password" name="contrasena" placeholder="Tu contraseña" required>
            </div>
            
            <button type="submit" name="enviar" class="boton-enviar-login">Iniciar Sesión</button>
        </form>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; font-size: 12px; color: #999;">
            <p>Sistema de Gestión Escolar v1.0</p>
        </div>
    </div>
</div>

<script>
// Manejar selección de rol
document.querySelectorAll('.boton-rol').forEach(boton => {
    boton.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('.boton-rol').forEach(b => b.classList.remove('activo'));
        this.classList.add('activo');
        document.getElementById('tipoUsuario').value = this.dataset.rol;
    });
});
</script>

</body>
</html>
