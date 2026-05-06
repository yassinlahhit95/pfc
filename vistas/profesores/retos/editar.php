<?php
session_start();

$error = $_SESSION['error'] ?? null;
$exito = $_SESSION['exito'] ?? null;
$errs = $_SESSION['errores'] ?? [];
unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores']);

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../login.php");
    exit;
}

require_once "../../../modelos/retos.php";

$id = $_GET['id'] ?? '';
$reto = obtenerRetoPorId($id);

if (!$reto) {
    header("Location: lista.php");
    exit;
}

$tituloPagina = "Editar Reto - Portal Profesores";
$seccionActual = 'retos';
include_once "../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Editar Reto</h1>
    <a href="lista.php" class="boton-secundario">← VOLVER</a>
</div>

<?php if ($error): ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php endif; ?>
<?php if ($exito): ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php endif; ?>

<div class="tarjeta-blanca">
    <form action="../../../controladores/profesores/retos/actualizar.php" method="POST" class="form-estandar">
        <input type="hidden" name="idReto" value="<?= $id ?>">
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre del Reto *</label>
                <input type="text" name="nombreReto" value="<?= $reto['nombreReto'] ?>" class="<?= isset($errs['nombreReto']) ? 'input-error' : '' ?>">
                <?php if (isset($errs['nombreReto'])): ?>
                    <strong class="error-campo"><?= $errs['nombreReto'] ?></strong>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Horas Totales *</label>
                <input type="text" name="horasReto" value="<?= $reto['horasReto'] ?>" class="<?= isset($errs['horasReto']) ? 'input-error' : '' ?>">
                <?php if (isset($errs['horasReto'])): ?>
                    <strong class="error-campo"><?= $errs['horasReto'] ?></strong>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Fecha Inicio *</label>
                <input type="date" name="fechaInicio" value="<?= $reto['fechaInicio'] ?>" class="<?= isset($errs['fechaInicio']) ? 'input-error' : '' ?>">
                <?php if (isset($errs['fechaInicio'])): ?>
                    <strong class="error-campo"><?= $errs['fechaInicio'] ?></strong>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Fecha Fin *</label>
                <input type="date" name="fechaFin" value="<?= $reto['fechaFin'] ?>" class="<?= isset($errs['fechaFin']) ? 'input-error' : '' ?>">
                <?php if (isset($errs['fechaFin'])): ?>
                    <strong class="error-campo"><?= $errs['fechaFin'] ?></strong>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-acciones mt-20">
            <button type="submit" name="actualizarReto" class="boton-primario">
                <i class="fas fa-save"></i> ACTUALIZAR RETO
            </button>
            <button type="button" class="boton-secundario px-25" onclick="window.location.href = window.location.pathname + window.location.search;">
                <i class="fas fa-eraser"></i> LIMPIAR
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>

