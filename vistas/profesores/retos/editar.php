<?php
require_once __DIR__ . "/../../../include/Security.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/retos.php";

$idReto = $_GET['id'] ?? '';
$reto = obtenerRetoPorId($idReto);

if (!$reto) {
    header("Location: lista.php");
    exit;
}

$idProfesor = $_SESSION['idProfesor'];
$misModulos = listarModulosDeProfesor($idProfesor);

$modulosAsociados = listarModulosDeReto($idReto);
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

<?php if ($errores) { ?>
    <div class="mensaje-error"><?= Security::escapeHtml($errores ) ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= Security::escapeHtml($exito ) ?></div>
<?php } ?>

<div class="panel">
    <form action="../../../controladores/profesores/retos/actualizar.php" method="POST" class="formulario">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idReto" value="<?= Security::escapeHtml($idReto ) ?>">

        <div class="campo">
            <label for="nombreReto">Nombre del Reto</label>
            <input type="text" name="nombreReto" id="nombreReto" value="<?= Security::escapeHtml($reto['nombreReto'] ) ?>">
        </div>

        <div class="campo">
            <label for="horasReto">Horas Totales</label>
            <input type="number" name="horasReto" id="horasReto" value="<?= Security::escapeHtml($reto['horasReto'] ) ?>">
        </div>

        <div class="campo">
            <label for="fechaInicio">Fecha Inicio</label>
            <input type="date" name="fechaInicio" id="fechaInicio" value="<?= Security::escapeHtml($reto['fechaInicio'] ) ?>">
        </div>

        <div class="campo">
            <label for="fechaFin">Fecha Fin</label>
            <input type="date" name="fechaFin" id="fechaFin" value="<?= Security::escapeHtml($reto['fechaFin'] ) ?>">
        </div>

        <div class="campo">
            <label>Asociar a Módulos</label>
            <p class="texto-suave" style="margin-bottom: 10px;">Seleccione los modulos en los que se evaluare este reto.</p>
            <div class="checks scroll-v200">
                <?php foreach ($misModulos as $mod) { ?>
                    <label class="check-item" for="mod_<?= Security::escapeHtml($mod['idModulo'] ) ?>">
                        <input type="checkbox" name="modulos[]" id="mod_<?= Security::escapeHtml($mod['idModulo'] ) ?>" value="<?= Security::escapeHtml($mod['idModulo'] ) ?>" 
                            <?= Security::escapeHtml(isset($mapaModulosAsociados[$mod['idModulo']]) ? 'checked' : '') ?>>
                        <span><?= Security::escapeHtml($mod['nombreModulo'] ) ?> (<?= Security::escapeHtml($mod['abreviaturaCiclo'] ) ?>)</span>
                    </label>
                <?php } ?>
            </div>
            
        </div>

        <div class="acciones" style="margin-top: 20px;">
            <input type="submit" name="actualizarReto" class="boton-primario" value="GUARDAR CAMBIOS">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>


