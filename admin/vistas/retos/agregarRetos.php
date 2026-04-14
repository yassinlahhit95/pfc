<?php
session_start();
$titulo_pagina = "Agregar Reto - Super Admin";
$seccion = 'retos';
include_once "../comunes/nav.php";

require_once "../../modelos/modulos.php";

$conexionObj = new Conexion();
$conexion = $conexionObj->conectar();
$moduloObj = new modulo($conexion);
$listaModulos = $moduloObj->listarModulosModelo();

$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_reto'] ?? [];
unset($_SESSION['errores'], $_SESSION['datos_reto']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Agregar Reto</h1>
        <p class="subtitulo-encabezado">Crear un nuevo reto o desafío</p>
    </div>
    <div class="acciones-pagina">
        <a href="vistas/retos/verRetos.php" class="boton-secundario">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="contenedor-formulario">
    <form action="controlador/retosControlador.php" method="POST" class="formulario-estandar">
        <input type="hidden" name="accion" value="insertar">
        
        <div class="grupo-formulario">
            <label for="nombreReto">Nombre del Reto *</label>
            <input type="text" id="nombreReto" name="nombreReto" 
                   value="<?php echo htmlspecialchars($datos['nombreReto'] ?? ''); ?>">
            <?php if (isset($errores['nombreReto'])): ?>
                <span style="color: red; font-size: 14px;"><?php echo $errores['nombreReto']; ?></span>
            <?php endif; ?>
        </div>

        <div class="dos-columnas">
            <div class="grupo-formulario">
                <label for="fechaInicio">Fecha Inicio *</label>
                <input type="date" id="fechaInicio" name="fechaInicio" 
                       value="<?php echo htmlspecialchars($datos['fechaInicio'] ?? date('Y-m-d')); ?>">
                <?php if (isset($errores['fechaInicio'])): ?>
                    <span style="color: red; font-size: 14px;"><?php echo $errores['fechaInicio']; ?></span>
                <?php endif; ?>
            </div>
            <div class="grupo-formulario">
                <label for="fechaFin">Fecha Fin *</label>
                <input type="date" id="fechaFin" name="fechaFin" 
                       value="<?php echo htmlspecialchars($datos['fechaFin'] ?? date('Y-m-d', strtotime('+7 days'))); ?>">
                <?php if (isset($errores['fechaFin'])): ?>
                    <span style="color: red; font-size: 14px;"><?php echo $errores['fechaFin']; ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="grupo-formulario">
            <label for="horasReto">Número de Horas *</label>
            <input type="number" id="horasReto" name="horasReto" 
                   value="<?php echo htmlspecialchars($datos['horasReto'] ?? '10'); ?>">
            <?php if (isset($errores['horasReto'])): ?>
                <span style="color: red; font-size: 14px;"><?php echo $errores['horasReto']; ?></span>
            <?php endif; ?>
        </div>

        <div class="grupo-formulario">
            <label>Módulos Asociados * (Selecciona al menos uno)</label>
            <div class="lista-checkboxes" style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                <?php foreach ($listaModulos as $m) { ?>
                    <div class="item-checkbox" style="margin-bottom: 5px;">
                        <input type="checkbox" name="modulos[]" value="<?php echo $m['idModulo']; ?>" 
                            id="mod_<?php echo $m['idModulo']; ?>"
                            <?php echo (isset($datos['modulos']) && in_array($m['idModulo'], $datos['modulos'])) ? 'checked' : ''; ?>>
                        <label for="mod_<?php echo $m['idModulo']; ?>">
                            <?php echo htmlspecialchars($m['nombreModulo']); ?> (<?php echo htmlspecialchars($m['nombreCiclo']); ?>)
                        </label>
                    </div>
                <?php } ?>
                <?php if (empty($listaModulos)): ?>
                    <p class="sin-datos">No hay módulos disponibles. Crea uno primero.</p>
                <?php endif; ?>
            </div>
            <?php if (isset($errores['modulos'])): ?>
                <span style="color: red; font-size: 14px;"><?php echo $errores['modulos']; ?></span>
            <?php endif; ?>
        </div>

        <div class="botones-formulario">
            <button type="submit" name="guardarReto" class="boton-primario">Guardar Reto</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
