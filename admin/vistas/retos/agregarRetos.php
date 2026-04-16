<?php
session_start();
$titulo_pagina = "Agregar Reto - Super Admin";
$seccion = 'retos';
include_once "../comunes/nav.php";

require_once "../../modelos/modulos.php";

$moduloObj = new modulo();
$listaModulos = $moduloObj->listarModulosModelo();

$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_reto'] ?? [];
unset($_SESSION['errores'], $_SESSION['datos_reto']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Agregar Reto</h1>
        <p class="subtitulo-encabezado">Diseñar un nuevo reto educativo para el alumnado</p>
    </div>
    <div class="acciones-pagina">
        <a href="vistas/retos/verRetos.php" class="boton-secundario">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-tasks color-primary mr-10"></i> Configuración del Reto</h3>
    </div>
    <form action="controladores/retos/insertar.php" method="POST">
        <div class="formulario-cuadricula">
            <div class="campo-formulario campo-ancho-total">
                <label for="nombreReto">Nombre del Reto *</label>
                <input type="text" id="nombreReto" name="nombreReto" 
                       placeholder="Ej: Desarrollo de una API REST"
                       value="<?php echo htmlspecialchars($datos['nombreReto'] ?? ''); ?>"
                       class="<?php echo isset($errores['nombreReto']) ? 'input-error' : ''; ?>">
                <?php if (isset($errores['nombreReto'])): ?>
                    <span class="error-campo"><?php echo $errores['nombreReto']; ?></span>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label for="fechaInicio">Fecha de Inicio *</label>
                <input type="date" id="fechaInicio" name="fechaInicio" 
                       value="<?php echo htmlspecialchars($datos['fechaInicio'] ?? date('Y-m-d')); ?>"
                       class="<?php echo isset($errores['fechaInicio']) ? 'input-error' : ''; ?>">
                <?php if (isset($errores['fechaInicio'])): ?>
                    <span class="error-campo"><?php echo $errores['fechaInicio']; ?></span>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label for="fechaFin">Fecha de Finalización *</label>
                <input type="date" id="fechaFin" name="fechaFin" 
                       value="<?php echo htmlspecialchars($datos['fechaFin'] ?? date('Y-m-d', strtotime('+7 days'))); ?>"
                       class="<?php echo isset($errores['fechaFin']) ? 'input-error' : ''; ?>">
                <?php if (isset($errores['fechaFin'])): ?>
                    <span class="error-campo"><?php echo $errores['fechaFin']; ?></span>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label for="horasReto">Carga Horaria (Horas) *</label>
                <input type="text" id="horasReto" name="horasReto" 
                       placeholder="Ej: 20"
                       value="<?php echo htmlspecialchars($datos['horasReto'] ?? '10'); ?>"
                       class="<?php echo isset($errores['horasReto']) ? 'input-error' : ''; ?>">
                <?php if (isset($errores['horasReto'])): ?>
                    <span class="error-campo"><?php echo $errores['horasReto']; ?></span>
                <?php endif; ?>
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Módulos Asociados * <span class="texto-atenuado">(Selecciona al menos uno)</span></label>
                <div class="tarjeta-blanca sin-margen" style="background: #f8fafc; border: 1px solid #e2e8f0; max-height: 250px; overflow-y: auto; padding: 15px;">
                    <?php if (empty($listaModulos)): ?>
                        <p class="sin-datos">No hay módulos disponibles. Crea uno primero.</p>
                    <?php else: ?>
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 10px;">
                            <?php foreach ($listaModulos as $m) { ?>
                                <label class="disposicion-flexible alinear-centro separacion-pequena cursor-pointer" style="padding: 8px; border-radius: 6px; background: white; border: 1px solid #edf2f7;">
                                    <input type="checkbox" name="modulos[]" value="<?php echo $m['idModulo']; ?>" 
                                        <?php echo (isset($datos['modulos']) && in_array($m['idModulo'], $datos['modulos'])) ? 'checked' : ''; ?>>
                                    <span class="texto-pequeno">
                                        <strong><?php echo htmlspecialchars($m['nombreModulo']); ?></strong><br>
                                        <small class="texto-atenuado"><?php echo htmlspecialchars($m['nombreCiclo']); ?></small>
                                    </span>
                                </label>
                            <?php } ?>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if (isset($errores['modulos'])): ?>
                    <span class="error-campo"><?php echo $errores['modulos']; ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="guardarReto" class="boton-primario">
                <i class="fas fa-save"></i> Crear Reto Educativo
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
