<?php
session_start();
if (!isset($_SESSION['idAdmin'])) {
    header("Location: /pfc/index.php");
    exit;
}

$titulo_pagina = "Enviar Notificación Global - Super Admin";
$seccion = 'push';
include '../comunes/nav.php';
?>

<div class="encabezado-pagina">
    <h1>Notificaciones Push Globales</h1>
    <p class="subtitulo">Envía avisos en tiempo real a los dispositivos de los usuarios.</p>
</div>

<?php if (isset($_SESSION['exito'])) { ?>
    <div class="alerta alerta-exito">
        <i class="fas fa-check-circle"></i> <?php echo $_SESSION['exito']; unset($_SESSION['exito']); ?>
    </div>
<?php } ?>

<?php if (isset($_SESSION['error'])) { ?>
    <div class="alerta alerta-error">
        <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="info-importante">
        <i class="fas fa-info-circle"></i>
        <p>Las notificaciones se enviarán únicamente a los usuarios que hayan concedido permisos de notificación en sus navegadores.</p>
    </div>

    <form action="/pfc/controladores/admin/mensajes/notificacion_global.php" method="POST" class="formulario-estandar">
        <div class="campo-formulario">
            <label>Título de la Notificación *</label>
            <input type="text" name="titulo" required placeholder="Ej: Nueva circular disponible" class="control-formulario">
        </div>
        
        <div class="campo-formulario">
            <label>Mensaje / Contenido *</label>
            <textarea name="mensaje" required placeholder="Escribe el contenido de la notificación..." rows="4" class="control-formulario"></textarea>
        </div>
        
        <div class="campo-formulario">
            <label>Dirigido a *</label>
            <select name="dirigidoA" class="control-formulario">
                <option value="todos">Todos los usuarios del centro</option>
                <option value="estudiantes">Solo Estudiantes</option>
                <option value="profesores">Solo Profesores</option>
            </select>
        </div>
        
        <div class="margen-arriba separacion-superior">
            <button type="submit" name="enviarGlobal" class="boton-primario">
                <i class="fas fa-paper-plane"></i> Enviar Notificación Ahora
            </button>
        </div>
    </form>
</div>

<style>
.info-importante {
    display: flex;
    align-items: center;
    gap: 15px;
    background: #e3f2fd;
    padding: 15px;
    border-radius: 8px;
    color: #1976d2;
    margin-bottom: 25px;
}
.info-importante i {
    font-size: 1.2rem;
}
.subtitulo {
    color: #666;
    margin-top: 5px;
}
.separacion-superior {
    border-top: 1px solid #eee;
    padding-top: 20px;
}
.alerta {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.alerta-exito {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}
.alerta-error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>

<?php include '../comunes/footer.php'; ?>
