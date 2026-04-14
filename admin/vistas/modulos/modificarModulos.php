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

$conexionObj = new Conexion();
$conexion = $conexionObj->conectar();
$moduloObj = new modulo($conexion);
$moduloActual = $moduloObj->obtenerModuloPorIdModelo($id);

if (!$moduloActual) {
    header("Location: verModulos.php");
    exit;
}

$cicloObj = new ciclo($conexion);
$listaCiclos = $cicloObj->listarCiclosModelo();

$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['errores']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Modificar Módulo</h1>
        <p class="subtitulo-encabezado">Editando: <?php echo htmlspecialchars($moduloActual['nombreModulo']); ?></p>
    </div>
    <div class="acciones-pagina">
        <a href="vistas/modulos/verModulos.php" class="boton-secundario">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="contenedor-formulario">
    <form action="controlador/modulosControlador.php" method="POST" class="formulario-estandar">
        <input type="hidden" name="accion" value="actualizar">
        <input type="hidden" name="idModulo" value="<?php echo $moduloActual['idModulo']; ?>">
        
        <div class="grupo-formulario">
            <label for="nombreModulo">Nombre del Módulo *</label>
            <input type="text" id="nombreModulo" name="nombreModulo" 
                   value="<?php echo htmlspecialchars($moduloActual['nombreModulo']); ?>">
            <?php if (isset($errores['nombreModulo'])): ?>
                <span style="color: red; font-size: 14px;"><?php echo $errores['nombreModulo']; ?></span>
            <?php endif; ?>
        </div>

        <div class="grupo-formulario">
            <label for="idCiclo">Ciclo *</label>
            <select id="idCiclo" name="idCiclo">
                <option value="">Seleccione un ciclo</option>
                <?php foreach ($listaCiclos as $c) { ?>
                    <option value="<?php echo $c['idCiclo']; ?>" 
                        <?php echo ($moduloActual['idCiclo'] == $c['idCiclo']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($c['nombreCiclo']); ?>
                    </option>
                <?php } ?>
            </select>
            <?php if (isset($errores['idCiclo'])): ?>
                <span style="color: red; font-size: 14px;"><?php echo $errores['idCiclo']; ?></span>
            <?php endif; ?>
        </div>

        <div class="grupo-formulario">
            <label for="horasMaximas">Horas Máximas *</label>
            <input type="number" id="horasMaximas" name="horasMaximas" 
                   value="<?php echo htmlspecialchars($moduloActual['horasMaximas']); ?>">
            <?php if (isset($errores['horasMaximas'])): ?>
                <span style="color: red; font-size: 14px;"><?php echo $errores['horasMaximas']; ?></span>
            <?php endif; ?>
        </div>

        <div class="botones-formulario">
            <button type="submit" name="guardarModulo" class="boton-primario">Actualizar Módulo</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
