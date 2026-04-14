<?php
session_start();
$titulo_pagina = "Modificar Reto - Super Admin";
$seccion = 'retos';
include_once "../comunes/nav.php";

require_once "../../modelos/retos.php";
require_once "../../modelos/modulos.php";

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: verRetos.php");
    exit;
}

$conexionObj = new Conexion();
$conexion = $conexionObj->conectar();
$retoObj = new reto($conexion);
$retoActual = $retoObj->obtenerRetoPorIdModelo($id);

if (!$retoActual) {
    header("Location: verRetos.php");
    exit;
}

$moduloObj = new modulo($conexion);
$listaModulos = $moduloObj->listarModulosModelo();
$modulosDeReto = $retoObj->obtenerModulosDeReto($id);
$idsDeReto = array_column($modulosDeReto, 'idModulo');

$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['errores']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Modificar Reto</h1>
        <p class="subtitulo-encabezado">Editando: <?php echo htmlspecialchars($retoActual['nombreReto']); ?></p>
    </div>
    <div class="acciones-pagina">
        <a href="vistas/retos/verRetos.php" class="boton-secundario">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="contenedor-formulario">
    <form action="controlador/retosControlador.php" method="POST" class="formulario-estandar">
        <input type="hidden" name="accion" value="actualizar">
        <input type="hidden" name="idReto" value="<?php echo $retoActual['idReto']; ?>">
        
        <div class="grupo-formulario">
            <label for="nombreReto">Nombre del Reto *</label>
            <input type="text" id="nombreReto" name="nombreReto" 
                   value="<?php echo htmlspecialchars($retoActual['nombreReto']); ?>">
            <?php if (isset($errores['nombreReto'])): ?>
                <span style="color: red; font-size: 14px;"><?php echo $errores['nombreReto']; ?></span>
            <?php endif; ?>
        </div>

        <div class="dos-columnas">
            <div class="grupo-formulario">
                <label for="fechaInicio">Fecha Inicio *</label>
                <input type="date" id="fechaInicio" name="fechaInicio" 
                       value="<?php echo htmlspecialchars($retoActual['fechaInicio']); ?>">
                <?php if (isset($errores['fechaInicio'])): ?>
                    <span style="color: red; font-size: 14px;"><?php echo $errores['fechaInicio']; ?></span>
                <?php endif; ?>
            </div>
            <div class="grupo-formulario">
                <label for="fechaFin">Fecha Fin *</label>
                <input type="date" id="fechaFin" name="fechaFin" 
                       value="<?php echo htmlspecialchars($retoActual['fechaFin']); ?>">
                <?php if (isset($errores['fechaFin'])): ?>
                    <span style="color: red; font-size: 14px;"><?php echo $errores['fechaFin']; ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="grupo-formulario">
            <label for="horasReto">Número de Horas *</label>
            <input type="number" id="horasReto" name="horasReto" 
                   value="<?php echo htmlspecialchars($retoActual['horasReto']); ?>">
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
                            <?php echo (in_array($m['idModulo'], $idsDeReto)) ? 'checked' : ''; ?>>
                        <label for="mod_<?php echo $m['idModulo']; ?>">
                            <?php echo htmlspecialchars($m['nombreModulo']); ?> (<?php echo htmlspecialchars($m['nombreCiclo']); ?>)
                        </label>
                    </div>
                <?php } ?>
            </div>
            <?php if (isset($errores['modulos'])): ?>
                <span style="color: red; font-size: 14px;"><?php echo $errores['modulos']; ?></span>
            <?php endif; ?>
        </div>

        <div class="botones-formulario">
            <button type="submit" name="guardarReto" class="boton-primario">Actualizar Reto</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
