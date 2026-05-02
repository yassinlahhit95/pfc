<?php
session_start();

$error = $_SESSION['error'] ?? null;
$exito = $_SESSION['exito'] ?? null;
unset($_SESSION['error'], $_SESSION['exito']);

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../../index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/retos.php";

$id = $_GET['id'] ?? '';
$reto = obtenerRetoPorId($id);

$tituloDelPagina = "Editar Reto - Portal Profesores";
$seccionActual = 'retos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Editar Reto</h1>
    <a href="lista.php" class="boton-secundario">← Volver</a>
</div>

<?php if ($error) : ?>
    <div class="alerta-error"><?= $error ?></div>
<?php endif; ?>
<?php if ($exito) : ?>
    <div class="alerta-exito"><?= $exito ?></div>
<?php endif; ?>

<div class="tarjeta-blanca">
    <form action="../../../controladores/profesores/retos/actualizar.php" method="POST">
        <input type="hidden" name="idReto" value="<?= $id ?>">
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre del Reto *</label>
                <input type="text" name="nombreReto" value="<?= $reto['nombreReto'] ?>">
            </div>

            <div class="campo-formulario">
                <label>Horas Totales *</label>
                <input type="text" name="horasReto" value="<?= $reto['horasReto'] ?>">
            </div>

            <div class="campo-formulario">
                <label>Fecha Inicio *</label>
                <input type="date" name="fechaInicio" value="<?= $reto['fechaInicio'] ?>">
            </div>

            <div class="campo-formulario">
                <label>Fecha Fin *</label>
                <input type="date" name="fechaFin" value="<?= $reto['fechaFin'] ?>">
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="actualizarReto" class="boton-primario">Actualizar Reto</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>

