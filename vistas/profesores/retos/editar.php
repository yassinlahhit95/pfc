<?php
session_start();

$error = $_SESSION['error'] ?? null;
$exito = $_SESSION['exito'] ?? null;
$errs = $_SESSION['errores'] ?? [];
unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/retos.php";

$id = $_GET['id'] ?? '';
$reto = obtenerRetoPorId($id);

if (!$reto) {
    header("Location: lista.php");
    exit;
}

$idProfesor = $_SESSION['idProfesor'];
$misModulos = listarModulosDeProfesor($idProfesor);

$modulosAsociados = listarModulosDeReto($id);
$mapaModulosAsociados = [];
foreach ($modulosAsociados as $modAsociado) { $mapaModulosAsociados[$modAsociado['idModulo']] = true; }

$tituloDelPagina = "AULAPRO | EDITAR RETO";
$seccionActual = 'retos';
include_once "../comunes/nav.php";
?>

<div class="cabecera">
    <h1>EDITAR RETO</h1>
    <a href="lista.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<div class="panel">
    <form action="../../../controladores/profesores/retos/actualizar.php" method="POST" class="formulario">
        <input type="hidden" name="idReto" value="<?= $id ?>">
        
        <div class="campo">
            <label for="nombreReto">Nombre del Reto *</label>
            <input type="text" name="nombreReto" id="nombreReto" value="<?= $reto['nombreReto'] ?>" class="<?= isset($errs['nombreReto']) ? 'input-error' : '' ?>">
            <?php if (isset($errs['nombreReto'])) { ?>
                <strong class="error-campo"><?= $errs['nombreReto'] ?></b>
            <?php } ?>
        </div>

        <div class="campo">
            <label for="horasReto">Horas Totales *</label>
            <input type="text" name="horasReto" id="horasReto" value="<?= $reto['horasReto'] ?>" class="<?= isset($errs['horasReto']) ? 'input-error' : '' ?>">
            <?php if (isset($errs['horasReto'])) { ?>
                <strong class="error-campo"><?= $errs['horasReto'] ?></b>
            <?php } ?>
        </div>

        <div class="campo">
            <label for="fechaInicio">Fecha Inicio *</label>
            <input type="date" name="fechaInicio" id="fechaInicio" value="<?= $reto['fechaInicio'] ?>" class="<?= isset($errs['fechaInicio']) ? 'input-error' : '' ?>">
            <?php if (isset($errs['fechaInicio'])) { ?>
                <strong class="error-campo"><?= $errs['fechaInicio'] ?></b>
            <?php } ?>
        </div>

        <div class="campo">
            <label for="fechaFin">Fecha Fin *</label>
            <input type="date" name="fechaFin" id="fechaFin" value="<?= $reto['fechaFin'] ?>" class="<?= isset($errs['fechaFin']) ? 'input-error' : '' ?>">
            <?php if (isset($errs['fechaFin'])) { ?>
                <strong class="error-campo"><?= $errs['fechaFin'] ?></b>
            <?php } ?>
        </div>

        <div class="campo">
            <label>Asociar a Módulos *</label>
            <p class="atenuado" style="margin-bottom: 10px;">Seleccione los módulos en los que se evaluará este reto.</p>
            <div class="checks scroll-v200">
                <?php foreach ($misModulos as $mod) { ?>
                    <label class="check-item" for="mod_<?= $mod['idModulo'] ?>">
                        <input type="checkbox" name="modulos[]" id="mod_<?= $mod['idModulo'] ?>" value="<?= $mod['idModulo'] ?>" 
                            <?= isset($mapaModulosAsociados[$mod['idModulo']]) ? 'checked' : '' ?>>
                        <span><?= $mod['nombreModulo'] ?> (<?= $mod['abreviaturaCiclo'] ?>)</span>
                    </label>
                <?php } ?>
            </div>
            <?php if (isset($errs['modulos'])) { ?>
                <strong class="error-campo"><?= $errs['modulos'] ?></b>
            <?php } ?>
        </div>

        <div class="acciones" style="margin-top: 20px;">
            <button type="submit" name="actualizarReto" class="boton-primario">
                <i class="fas fa-save"></i> GUARDAR CAMBIOS
            </button>
            <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname + window.location.search;">
                <i class="fas fa-eraser"></i> LIMPIAR
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
