<?php
session_start();
$titulo_pagina = "Modificar Ciclo - Super Admin";
$seccion = 'ciclos';
include_once "../comunes/nav.php";

require_once "../../modelos/ciclos.php";

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: verCiclos.php");
    exit;
}

$conexionObj = new Conexion();
$conexion = $conexionObj->conectar();
$cicloObj = new ciclo($conexion);
$cicloActual = $cicloObj->obtenerCicloPorIdModelo($id);

if (!$cicloActual) {
    header("Location: verCiclos.php");
    exit;
}

$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['errores']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Modificar Ciclo</h1>
        <p class="subtitulo-encabezado">Editando: <?php echo htmlspecialchars($cicloActual['nombreCiclo']); ?></p>
    </div>
    <div class="acciones-pagina">
        <a href="vistas/ciclos/verCiclos.php" class="boton-secundario">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="contenedor-formulario">
    <form action="controlador/ciclosControlador.php" method="POST" class="formulario-estandar">
        <input type="hidden" name="accion" value="actualizar">
        <input type="hidden" name="idCiclo" value="<?php echo $cicloActual['idCiclo']; ?>">
        
        <div class="grupo-formulario">
            <label for="nombreCiclo">Nombre del Ciclo *</label>
            <input type="text" id="nombreCiclo" name="nombreCiclo" 
                   value="<?php echo htmlspecialchars($cicloActual['nombreCiclo']); ?>">
            <?php if (isset($errores['nombreCiclo'])): ?>
                <span style="color: red; font-size: 14px;"><?php echo $errores['nombreCiclo']; ?></span>
            <?php endif; ?>
        </div>

        <div class="grupo-formulario">
            <label for="descripcionCiclo">Descripción *</label>
            <textarea id="descripcionCiclo" name="descripcionCiclo" rows="4"><?php echo htmlspecialchars($cicloActual['descripcionCiclo']); ?></textarea>
            <?php if (isset($errores['descripcionCiclo'])): ?>
                <span style="color: red; font-size: 14px;"><?php echo $errores['descripcionCiclo']; ?></span>
            <?php endif; ?>
        </div>

        <div class="botones-formulario">
            <button type="submit" name="guardarCiclo" class="boton-primario">Actualizar Ciclo</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
