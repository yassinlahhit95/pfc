<?php
session_start();
$titulo_pagina = "Modificar Módulo - Super Admin";
$seccion = 'modulos';
include_once "../comunes/nav.php";

require_once "../../modelos/modulos.php";
require_once "../../modelos/ciclos.php";

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: verModulos.php");
    exit;
}

$moduloObj = new modulo();
$moduloActual = $moduloObj->obtenerModuloPorIdModelo($id);

if (!$moduloActual) {
    header("Location: verModulos.php");
    exit;
}

$cicloObj = new ciclo();
$listaCiclos = $cicloObj->listarCiclosModelo();

$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['errores']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Modificar Módulo</h1>
        <p class="subtitulo-encabezado">Actualizando información de: <strong><?php echo htmlspecialchars($moduloActual['nombreModulo']); ?></strong></p>
    </div>
    <div class="acciones-pagina">
        <a href="vistas/modulos/verModulos.php" class="boton-secundario">
            <i class="fas fa-arrow-left"></i> Volver al listado
        </a>
    </div>
</div>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-edit color-primary mr-10"></i> Datos del Módulo</h3>
    </div>
    <form action="controladores/modulos/actualizar.php" method="POST">
        <input type="hidden" name="idModulo" value="<?php echo $moduloActual['idModulo']; ?>">
        
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label for="nombreModulo">Nombre del Módulo *</label>
                <input type="text" id="nombreModulo" name="nombreModulo" 
                       placeholder="Ej: Programación"
                       value="<?php echo htmlspecialchars($moduloActual['nombreModulo']); ?>"
                       class="<?php echo isset($errores['nombreModulo']) ? 'input-error' : ''; ?>">
                <?php if (isset($errores['nombreModulo'])): ?>
                    <span class="error-campo"><?php echo $errores['nombreModulo']; ?></span>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label for="idCiclo">Ciclo Formativo *</label>
                <select id="idCiclo" name="idCiclo" class="<?php echo isset($errores['idCiclo']) ? 'input-error' : ''; ?>">
                    <option value="">-- Seleccionar Ciclo --</option>
                    <?php foreach ($listaCiclos as $c) { ?>
                        <option value="<?php echo $c['idCiclo']; ?>" 
                            <?php echo ($moduloActual['idCiclo'] == $c['idCiclo']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($c['nombreCiclo']); ?>
                        </option>
                    <?php } ?>
                </select>
                <?php if (isset($errores['idCiclo'])): ?>
                    <span class="error-campo"><?php echo $errores['idCiclo']; ?></span>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label for="horasMaximas">Horas Máximas Anuales *</label>
                <input type="text" id="horasMaximas" name="horasMaximas" 
                       placeholder="Ej: 100"
                       value="<?php echo htmlspecialchars($moduloActual['horasMaximas']); ?>"
                       class="<?php echo isset($errores['horasMaximas']) ? 'input-error' : ''; ?>">
                <?php if (isset($errores['horasMaximas'])): ?>
                    <span class="error-campo"><?php echo $errores['horasMaximas']; ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="guardarModulo" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
