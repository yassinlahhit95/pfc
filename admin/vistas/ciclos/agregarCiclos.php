<?php
session_start();
$titulo_pagina = "Agregar Ciclo - Super Admin";
$seccion = 'ciclos';
include_once "../comunes/nav.php";

$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_ciclo'] ?? [];
unset($_SESSION['errores'], $_SESSION['datos_ciclo']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Agregar Ciclo</h1>
        <p class="subtitulo-encabezado">Crear un nuevo ciclo formativo</p>
    </div>
    <div class="acciones-pagina">
        <a href="vistas/ciclos/verCiclos.php" class="boton-secundario">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="contenedor-formulario">
    <form action="controlador/ciclosControlador.php" method="POST" class="formulario-estandar">
        <input type="hidden" name="accion" value="insertar">
        
        <div class="grupo-formulario">
            <label for="nombreCiclo">Nombre del Ciclo *</label>
            <input type="text" id="nombreCiclo" name="nombreCiclo" 
                   value="<?php echo htmlspecialchars($datos['nombreCiclo'] ?? ''); ?>">
            <?php if (isset($errores['nombreCiclo'])): ?>
                <span style="color: red; font-size: 14px;"><?php echo $errores['nombreCiclo']; ?></span>
            <?php endif; ?>
        </div>

        <div class="grupo-formulario">
            <label for="descripcionCiclo">Descripción *</label>
            <textarea id="descripcionCiclo" name="descripcionCiclo" rows="4"><?php echo htmlspecialchars($datos['descripcionCiclo'] ?? ''); ?></textarea>
            <?php if (isset($errores['descripcionCiclo'])): ?>
                <span style="color: red; font-size: 14px;"><?php echo $errores['descripcionCiclo']; ?></span>
            <?php endif; ?>
        </div>

        <div class="botones-formulario">
            <button type="submit" name="guardarCiclo" class="boton-primario">Guardar Ciclo</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
