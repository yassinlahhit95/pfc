<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/retos.php";

$id = $_GET['id'];
$reto = obtenerRetoPorId($id);

$tituloDelPagina = "Editar Reto - Portal Profesores";
$seccionActual = 'retos';
include_once "../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Editar Reto</h1>
    <a href="/pfc/vistas/profesores/retos/lista.php" class="boton-secundario">← Volver</a>
</div>

<div class="tarjeta-blanca">
    <form action="/pfc/controladores/profesores/retos/actualizar.php" method="POST">
        <input type="hidden" name="idReto" value="<?php echo $id; ?>">
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre del Reto *</label>
                <input type="text" name="nombreReto" value="<?php echo $reto['nombreReto']; ?>">
            </div>

            <div class="campo-formulario">
                <label>Horas Totales *</label>
                <input type="text" name="horasReto" value="<?php echo $reto['horasReto']; ?>">
            </div>

            <div class="campo-formulario">
                <label>Fecha Inicio *</label>
                <input type="date" name="fechaInicio" value="<?php echo $reto['fechaInicio']; ?>">
            </div>

            <div class="campo-formulario">
                <label>Fecha Fin *</label>
                <input type="date" name="fechaFin" value="<?php echo $reto['fechaFin']; ?>">
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="actualizarReto" class="boton-primario">Actualizar Reto</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>

