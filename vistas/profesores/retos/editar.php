<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

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

<?php if (is_string($errores) && $errores) { ?>
    <div class="mensaje-error"><?= $errores ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<div class="panel">
    <form action="../../../controladores/profesores/retos/actualizar.php" method="POST" class="formulario">
        <input type="hidden" name="idReto" value="<?= $id ?>">
        
        <div class="campo">
            <label for="nombreReto">Nombre del Reto</label>
            <input type="text" name="nombreReto" id="nombreReto" value="<?= $reto['nombreReto'] ?>" class="<?= isset($errores['nombreReto']) ? 'input-error' : '' ?>">
            <?php if (isset($errores['nombreReto'])) { ?>
                <strong class="error-campo"><?= $errores['nombreReto'] ?></strong>
            <?php } ?>
        </div>

        <div class="campo">
            <label for="horasReto">Horas Totales</label>
            <input type="text" name="horasReto" id="horasReto" value="<?= $reto['horasReto'] ?>" class="<?= isset($errores['horasReto']) ? 'input-error' : '' ?>">
            <?php if (isset($errores['horasReto'])) { ?>
                <strong class="error-campo"><?= $errores['horasReto'] ?></strong>
            <?php } ?>
        </div>

        <div class="campo">
            <label for="fechaInicio">Fecha Inicio</label>
            <input type="date" name="fechaInicio" id="fechaInicio" value="<?= $reto['fechaInicio'] ?>" class="<?= isset($errores['fechaInicio']) ? 'input-error' : '' ?>">
            <?php if (isset($errores['fechaInicio'])) { ?>
                <strong class="error-campo"><?= $errores['fechaInicio'] ?></strong>
            <?php } ?>
        </div>

        <div class="campo">
            <label for="fechaFin">Fecha Fin</label>
            <input type="date" name="fechaFin" id="fechaFin" value="<?= $reto['fechaFin'] ?>" class="<?= isset($errores['fechaFin']) ? 'input-error' : '' ?>">
            <?php if (isset($errores['fechaFin'])) { ?>
                <strong class="error-campo"><?= $errores['fechaFin'] ?></strong>
            <?php } ?>
        </div>

        <div class="campo">
            <label>Asociar a Módulos</label>
            <p class="texto-suave" style="margin-bottom: 10px;">Seleccione los modulos en los que se evaluare este reto.</p>
            <div class="checks scroll-v200">
                <?php foreach ($misModulos as $mod) { ?>
                    <label class="check-item" for="mod_<?= $mod['idModulo'] ?>">
                        <input type="checkbox" name="modulos[]" id="mod_<?= $mod['idModulo'] ?>" value="<?= $mod['idModulo'] ?>" 
                            <?= isset($mapaModulosAsociados[$mod['idModulo']]) ? 'checked' : '' ?>>
                        <span><?= $mod['nombreModulo'] ?> (<?= $mod['abreviaturaCiclo'] ?>)</span>
                    </label>
                <?php } ?>
            </div>
            <?php if (isset($errores['modulos'])) { ?>
                <strong class="error-campo"><?= $errores['modulos'] ?></strong>
            <?php } ?>
        </div>

        <div class="acciones" style="margin-top: 20px;">
            <input type="submit" name="actualizarReto" class="boton-primario" value="GUARDAR CAMBIOS">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
